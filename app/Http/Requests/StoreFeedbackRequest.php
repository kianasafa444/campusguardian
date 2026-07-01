<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_id' => ['required', 'string', 'exists:reports,tracking_id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'satisfaction_level' => ['required', 'string', Rule::in([
                'Sangat Tidak Puas', 'Tidak Puas', 'Netral', 'Puas', 'Sangat Puas',
            ])],
            'comment' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_id.required' => 'Tracking ID wajib diisi.',
            'tracking_id.exists' => 'Tracking ID tidak ditemukan.',
            'rating.required' => 'Rating wajib diisi.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
            'satisfaction_level.required' => 'Tingkat kepuasan wajib diisi.',
            'satisfaction_level.in' => 'Tingkat kepuasan tidak valid.',
        ];
    }
}
