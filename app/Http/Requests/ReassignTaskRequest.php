<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ReassignTaskRequest
 *
 * Handles the validation of requests for reassigning tasks.
 * This request ensures that the user input is validated before
 * processing the reassignment of a task.
 */
class ReassignTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized; otherwise, false.
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
            'assigned_to' => 'required|exists:users,id', // 'assigned_to' must be provided and must exist in the 'users' table.
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
            'assigned_to.required' => 'تعيين مستخدم للمهمة مطلوب', // Arabic: "Assigning a user to the task is required."
            'assigned_to.exists' => 'المستخدم المختار غير موجود', // Arabic: "The selected user does not exist."
        ];
    }
}
