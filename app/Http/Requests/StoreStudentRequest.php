<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentReques extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'document_type'   => 'required|in:TI,CC,RC',
            'document_number' => 'required|string|unique:students,document_number|max:20',
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'age'             => 'required|integer|min:3|max:20',
            'gender'          => 'required|string',
            'previous_school' => 'required|string|max:255',
            'grade'           => 'required|string|max:50',
            // Datos Acudiente
            'guardian_name'   => 'required|string|max:100',
            'guardian_lastname' => 'required|string|max:100',
            'guardian_document' => 'required|string|max:20',
            'guardian_age'    => 'required|integer',
            'guardian_phone'  => 'required|string|max:20',
            'guardian_address' => 'required|string|max:255',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_email'  => 'required|email|max:100',
            // Circuito
            'requested_areas' => 'required|array|min:1',
        ];
    }
}
