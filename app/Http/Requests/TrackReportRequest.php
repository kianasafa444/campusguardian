<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrackReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_id' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_id.required' => 'Tracking ID wajib diisi.',
        ];
    }
}
