<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_id' => ['required', 'string', 'exists:reports,tracking_id'],
            'support_type_id' => ['required', 'integer', 'exists:support_types,id'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_id.required' => 'Tracking ID wajib diisi.',
            'tracking_id.exists' => 'Tracking ID tidak ditemukan.',
            'support_type_id.required' => 'Jenis dukungan wajib dipilih.',
            'support_type_id.exists' => 'Jenis dukungan tidak valid.',
            'description.required' => 'Deskripsi kebutuhan wajib diisi.',
            'description.max' => 'Deskripsi maksimal 2000 karakter.',
        ];
    }
}
