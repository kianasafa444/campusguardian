<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    private const array ALLOWED_MIMES = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'video/mp4', 'video/webm', 'video/quicktime',
        'audio/mpeg', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/aac', 'audio/ogg', 'audio/webm',
        'application/pdf',
        'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evidence' => ['required', 'file', 'max:20480', 'mimes:jpeg,png,gif,webp,mp4,webm,mov,mp3,wav,m4a,aac,ogg,webm,pdf,doc,docx'],
        ];
    }

    public function messages(): array
    {
        return [
            'evidence.required' => 'File bukti wajib diupload.',
            'evidence.file' => 'File tidak valid.',
            'evidence.max' => 'Ukuran file maksimal 20MB.',
            'evidence.mimes' => 'Tipe file tidak diizinkan. Gunakan: JPG, PNG, GIF, WebP, MP4, MOV, MP3, WAV, M4A, AAC, OGG, PDF, DOC, DOCX.',
        ];
    }

    public static function getAllowedMimes(): array
    {
        return self::ALLOWED_MIMES;
    }
}
