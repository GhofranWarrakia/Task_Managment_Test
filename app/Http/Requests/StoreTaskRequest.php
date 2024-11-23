<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreTaskRequest
 *
 * Handles the validation of requests for storing tasks.
 * This request ensures that the user input for tasks meets specified validation criteria
 * before processing the storage of the task.
 */
class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:Bug,Feature,Improvement',
            'status' => 'required|in:Open,In Progress,Completed,Blocked',
            'priority' => 'required|in:Low,Medium,High',
            'due_date' => 'required|date|after:today',
            'assigned_to' => 'nullable|exists:users,id',
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
            'title.required' => 'عنوان المهمة مطلوب', // Arabic: "Task title is required."
            'due_date.after' => 'تاريخ التسليم يجب أن يكون بعد اليوم', // Arabic: "Due date must be after today."
        ];
    }
}
