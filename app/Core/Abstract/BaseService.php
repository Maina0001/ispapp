<?php

namespace App\Core\Abstract;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

abstract class BaseService
{
    /**
     * Wrap logic in a database transaction with automated logging.
     *
     * @param callable $callback
     * @return mixed
     * @throws Throwable
     */
    protected function transactional(callable $callback): mixed
    {
        DB::beginTransaction();

        try {
            $result = $callback();
            DB::commit();
            return $result;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError($e);
            throw $e;
        }
    }

    /**
     * Consistent error logging format across all modules.
     */
    protected function logError(Throwable $e, array $context = []): void
    {
        Log::error("[Service Error]: " . $e->getMessage(), array_merge([
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => substr($e->getTraceAsString(), 0, 500)
        ], $context));
    }

    /**
     * Format numbers to ISP standards (e.g., currency or bandwidth).
     */
    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 2);
    }
}