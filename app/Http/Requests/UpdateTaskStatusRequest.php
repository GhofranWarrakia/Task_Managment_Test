<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateTaskStatusRequest
 *
 * Handles the validation of requests for updating the status of tasks.
 * This request ensures that the user input for the task status meets specified validation criteria
 * before processing the update of the task status.
 */
class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized to perform this action; otherwise, false.
     */
    public function authorize(): bool
    {
        return true; // Allow all users to make this request.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     *         An associative array of validation rules for the request.
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:Open,In Progress,Completed,Blocked',
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     *
     * @return array<string, string> An associative array of custom error messages.
     */
    public function messages()
    {
        return [
            'status.required' => 'حالة المهمة مطلوبة', // Arabic: "Task status is required."
            'status.in' => 'الحالة يجب أن تكون واحدة من القيم التالية: Open, In Progress, Completed, Blocked', // Arabic: "Status must be one of the following values: Open, In Progress, Completed, Blocked."
        ];
    }
}
