<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource_category_id' => ['required', 'integer', 'exists:resource_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', Rule::in(['article', 'contact', 'faq'])],
            'is_published' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'resource_category_id.required' => 'Kategori resource wajib dipilih.',
            'resource_category_id.exists' => 'Kategori resource tidak valid.',
            'title.required' => 'Judul wajib diisi.',
            'content.required' => 'Konten wajib diisi.',
            'type.required' => 'Tipe resource wajib diisi.',
            'type.in' => 'Tipe resource harus article, contact, atau faq.',
        ];
    }
}
