<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreCommentRequest
 *
 * Handles the validation of requests for storing comments.
 * This request ensures that the user input for comments meets specified validation criteria
 * before processing the storage of the comment.
 */
class StoreCommentRequest extends FormRequest
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
            'content' => 'required|string|max:1000', // 'content' must be provided, be a string, and not exceed 1000 characters.
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
            'content.required' => 'نص التعليق مطلوب', // Arabic: "Comment content is required."
            'content.max' => 'نص التعليق يجب أن لا يتجاوز 1000 حرف', // Arabic: "Comment content must not exceed 1000 characters."
        ];
    }
}
