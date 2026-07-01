<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'incident_category_id' => ['required', 'integer', Rule::exists('incident_categories', 'id')],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'incident_date' => ['nullable', 'date'],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'incident_category_id.required' => 'Kategori insiden wajib dipilih.',
            'description.required' => 'Deskripsi laporan wajib diisi.',
            'description.min' => 'Deskripsi minimal 20 karakter.',
        ];
    }
}
