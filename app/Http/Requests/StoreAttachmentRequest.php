<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreAttachmentRequest
 *
 * Handles the validation of requests for storing file attachments.
 * This request ensures that the uploaded file meets specified validation criteria
 * before being processed for storage.
 */
class StoreAttachmentRequest extends FormRequest
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
     *         An associative array of validation rules for the file attachment.
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,pdf,docx|max:2048', // File is required, must be a file, allowed MIME types, and maximum size.
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     *
     * @return array<string, string> An associative array of custom error messages for validation failures.
     */
    public function messages()
    {
        return [
            'file.required' => 'المرفق مطلوب.', // Arabic: "The attachment is required."
            'file.mimes' => 'يجب أن يكون المرفق من نوع: jpeg, png, pdf, docx.', // Arabic: "The attachment must be of type: jpeg, png, pdf, docx."
            'file.max' => 'يجب ألا يتجاوز حجم المرفق 2 ميجابايت.', // Arabic: "The attachment must not exceed 2 megabytes."
        ];
    }
}
