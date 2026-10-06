<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Models\Subscription;
use Modules\Customer\Models\Customer;
use Modules\Network\Interfaces\NetworkDriverInterface;
use Modules\Network\Models\ServicePlan;
use Modules\Payments\Events\MpesaTransactionCompleted;
use Modules\Payments\Models\MpesaTransaction;
use Modules\Payments\Models\Payment;

class TestPaymentFlow extends Command
{
    protected $signature = 'isp:test-payment-flow {--prep-only : Only clean and bind the driver, then stop}';

    protected $description = 'End-to-end test of the payment → provisioning chain using a fake driver.';

    protected object $driver;
    protected string $token;

    public function handle(): int
    {
        $this->token = strtoupper(substr(md5(uniqid('', true)), 0, 6));

        $this->info('=== ISP Payment Flow Test ===');
        $this->line("   Run token: {$this->token}");

        $this->phase('1. Set queue driver to sync');
        config(['queue.default' => 'sync']);

        $this->phase('2. Bind fake network driver');
        $this->bindFakeDriver();

        $this->phase('3. Clean previous test data');
        $this->cleanup();

        if ($this->option('prep-only')) {
            $this->newLine();
            $this->warn('Prep-only mode: stopping here.');
            return self::SUCCESS;
        }

        $this->phase('4. Create test data');
        $customer = $this->createCustomer();
        $plan     = $this->createPlan();
        $invoice  = $this->createInvoice($customer);
        $txn      = $this->createMpesaTransaction($customer, $plan);

        $this->phase('5. Fire MpesaTransactionCompleted');
        event(new MpesaTransactionCompleted($txn));
        $this->line('   Event dispatched.');

        $this->phase('6. Verify each layer');
        $ok = $this->verify($customer, $invoice, $txn);

        $this->phase('7. Driver calls');
        $this->showDriverCalls();

        $this->newLine();
        if ($ok) {
            $this->info('✓ FULL CHAIN WORKS');
            return self::SUCCESS;
        }

        $this->error('✗ CHAIN BROKEN — see failures above');
        return self::FAILURE;
    }

    protected function bindFakeDriver(): void
    {
        $this->driver = new class implements NetworkDriverInterface {
            public array $calls = [];
            public function authenticateUser(string $identity, array $options = []): bool { $this->calls[] = ['authenticateUser', $identity, $options]; return true; }
            public function suspendUser(string $identity): bool { $this->calls[] = ['suspendUser', $identity]; return true; }
            public function resumeUser(string $identity): bool { $this->calls[] = ['resumeUser', $identity]; return true; }
            public function removeUser(string $identity): bool { $this->calls[] = ['removeUser', $identity]; return true; }
            public function updateBandwidth(string $identity, string $profileName): bool { $this->calls[] = ['updateBandwidth', $identity, $profileName]; return true; }
            public function getActiveSession(string $identity): ?array { return null; }
            public function disconnectUser(string $identity): void { $this->calls[] = ['disconnectUser', $identity]; }
        };

        app()->instance(NetworkDriverInterface::class, $this->driver);
        $this->line('   Fake driver bound.');
    }

    protected function cleanup(): void
    {
        $payments = Payment::where('gateway_reference', 'like', 'TESTRECEIPT%')->delete();
        $txns     = MpesaTransaction::where('checkout_request_id', 'like', 'TEST_%')->delete();

        // Test customers always have a locally-administered MAC (02:...)
        $customers = Customer::where('mac_address', 'like', '02:%')
            ->orWhere('email', 'like', 'test-%@example.com')
            ->get();

        $subs = 0;
        $invoices = 0;
        foreach ($customers as $customer) {
            $subs     += Subscription::where('customer_id', $customer->id)->delete();
            $invoices += Invoice::where('customer_id', $customer->id)->delete();
        }

        $customerIds = $customers->pluck('id');
        $customersDeleted = Customer::whereIn('id', $customerIds)->delete();

        $this->line("   Payments deleted: {$payments}");
        $this->line("   MpesaTransactions deleted: {$txns}");
        $this->line("   Subscriptions deleted: {$subs}");
        $this->line("   Invoices deleted: {$invoices}");
        $this->line("   Customers deleted: {$customersDeleted}");
    }

    protected function createCustomer(): Customer
    {
        $mac = '02:' . implode(':', array_map(
            fn () => str_pad(dechex(random_int(0, 255)), 2, '0', STR_PAD_LEFT),
            range(1, 5),
        ));

        $phone = '2547' . random_int(10000000, 99999999);

        $customer = Customer::create([
            'tenant_id'    => 1,
            'name'         => "Test Customer {$this->token}",
            'phone_number' => $phone,
            'email'        => "test-{$this->token}@example.com",
            'status'       => 'active',
            'mac_address'  => $mac,
        ]);

        $this->line("   Customer id={$customer->id}, phone={$phone}, mac={$mac}");
        return $customer;
    }

    protected function createPlan(): ServicePlan
    {
        $plan = ServicePlan::create([
            'tenant_id'        => 1,
            'name'             => "Test Plan {$this->token}",
            'price'            => 1000,
            'duration_minutes' => 43200,
            'bandwidth_limit'  => '10M/10M',
            'is_public'        => true,
        ]);

        $this->line("   Plan id={$plan->id}");
        return $plan;
    }

    protected function createInvoice(Customer $customer): Invoice
    {
        $invoice = Invoice::create([
            'tenant_id'      => 1,
            'customer_id'    => $customer->id,
            'invoice_number' => 'INV-TEST-' . $this->token . '-' . uniqid(),
            'amount'         => 1000,
            'tax_amount'     => 0,
            'total_amount'   => 1000,
            'balance'        => 1000,
            'status'         => 'unpaid',
            'due_date'       => now()->subDay(),
        ]);

        $this->line("   Invoice id={$invoice->id}, status={$invoice->status}");
        return $invoice;
    }

    protected function createMpesaTransaction(Customer $customer, ServicePlan $plan): MpesaTransaction
    {
        $txn = MpesaTransaction::create([
            'tenant_id'            => 1,
            'customer_id'          => $customer->id,
            'plan_id'              => $plan->id,
            'phone'                => $customer->phone_number,
            'amount'               => 1000,
            'merchant_request_id'  => 'MERCHANT_TEST_' . $this->token,
            'checkout_request_id'  => 'TEST_' . $this->token . '_' . uniqid(),
            'mpesa_receipt_number' => 'TESTRECEIPT_' . $this->token . '_' . uniqid(),
            'status'               => 'completed',
            'raw_payload'          => [
                'Body' => ['stkCallback' => [
                    'ResultCode' => 0,
                    'ResultDesc' => 'Success',
                    'CallbackMetadata' => ['Item' => [
                        ['Name' => 'Amount',             'Value' => 1000],
                        ['Name' => 'MpesaReceiptNumber', 'Value' => 'TESTRECEIPT123'],
                        ['Name' => 'PhoneNumber',        'Value' => (int) $customer->phone_number],
                    ]],
                ]],
            ],
        ]);

        $this->line("   MpesaTransaction id={$txn->id}, receipt={$txn->mpesa_receipt_number}");
        return $txn;
    }

    protected function verify(Customer $customer, Invoice $invoice, MpesaTransaction $txn): bool
    {
        $ok = true;

        $payment = Payment::where('gateway_reference', $txn->mpesa_receipt_number)->first();
        if ($payment) {
            $this->line("   ✓ Payment id={$payment->id}, amount={$payment->amount}, status={$payment->status}");
        } else {
            $this->error('   ✗ Payment NOT created');
            $ok = false;
        }

        $invoice->refresh();
        if ($invoice->status === 'paid') {
            $this->line("   ✓ Invoice status={$invoice->status}, balance={$invoice->balance}");
        } else {
            $this->error("   ✗ Invoice status={$invoice->status} (expected 'paid')");
            $ok = false;
        }

        $sub = Subscription::where('customer_id', $customer->id)->latest()->first();
        if ($sub) {
            $this->line("   ✓ Subscription id={$sub->id}, plan_id={$sub->plan_id}, expires={$sub->expires_at}");
        } else {
            $this->error('   ✗ Subscription NOT created');
            $ok = false;
        }

        return $ok;
    }

    protected function showDriverCalls(): void
    {
        if (empty($this->driver->calls)) {
            $this->warn('   (none — the driver was never called)');
            return;
        }
        foreach ($this->driver->calls as $i => $call) {
            $method = $call[0];
            $args   = array_slice($call, 1);
            $this->line("   [{$i}] {$method}(" . collect($args)->map(fn ($a) => is_array($a) ? json_encode($a) : $a)->implode(', ') . ')');
        }
    }

    protected function phase(string $title): void
    {
        $this->newLine();
        $this->info("--- {$title} ---");
    }
}