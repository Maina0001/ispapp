<?php

namespace Modules\Reporting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RevenueReportRequest
 * Validates parameters for generating revenue and collection reports.
 */
class RevenueReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to view financial reports.
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('view-financial-reports');
    }

    /**
     * Prepare data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'start_date' => $this->start_date ?? now()->startOfMonth()->format('Y-m-d'),
            'end_date'   => $this->end_date ?? now()->format('Y-m-d'),
            'format'     => strtolower($this->format ?? 'json'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date', 'before_or_equal:end_date'],
            'end_date'   => ['required', 'date', 'before_or_equal:today'],
            'group_by'   => ['nullable', 'in:day,week,month,payment_method'],
            'format'     => ['required', 'in:json,pdf,csv,xlsx'],
        ];
    }

    /**
     * Custom messages for validation.
     * @return array
     */
    public function messages(): array
    {
        return [
            'start_date.before_or_equal' => 'The start date cannot be after the end date.',
            'end_date.before_or_equal'   => 'The end date cannot be in the future.',
            'format.in'                  => 'Unsupported export format. Choose JSON, PDF, CSV, or XLSX.',
        ];
    }
}