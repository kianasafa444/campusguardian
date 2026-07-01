<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimelineNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:2000'],
            'is_admin_note' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Catatan wajib diisi.',
            'note.max' => 'Catatan maksimal 2000 karakter.',
        ];
    }
}
