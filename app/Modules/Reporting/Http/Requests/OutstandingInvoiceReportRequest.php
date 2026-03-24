<?php

namespace Modules\Reporting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class OutstandingInvoiceReportRequest
 * Validates filters for aging debt and unpaid invoice reports.
 */
class OutstandingInvoiceReportRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('view-billing-reports');
    }

    /**
     * Get the validation rules.
     * @return array
     */
    public function rules(): array
    {
        return [
            'min_days_overdue' => ['nullable', 'integer', 'min:0'],
            'min_amount'       => ['nullable', 'numeric', 'min:0'],
            'customer_status'  => ['nullable', 'in:active,suspended'],
            'include_voided'   => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'min_days_overdue.integer' => 'Overdue days must be a whole number.',
            'min_amount.numeric'       => 'The minimum amount must be a valid number.',
        ];
    }
}