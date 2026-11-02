<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSatisfactionSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'speed_rating' => ['required', 'integer', 'between:1,5'],
            'quality_rating' => ['required', 'integer', 'between:1,5'],
            'communication_rating' => ['required', 'integer', 'between:1,5'],
            'system_rating' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'speed_rating.required' => 'Mohon berikan rating kecepatan respon (1-5 bintang).',
            'quality_rating.required' => 'Mohon berikan rating kualitas solusi (1-5 bintang).',
            'communication_rating.required' => 'Mohon berikan rating komunikasi petugas (1-5 bintang).',
            'system_rating.required' => 'Mohon berikan rating kemudahan sistem (1-5 bintang).',
            'speed_rating.between' => 'Rating kecepatan harus bernilai 1 sampai 5.',
            'quality_rating.between' => 'Rating kualitas harus bernilai 1 sampai 5.',
            'communication_rating.between' => 'Rating komunikasi harus bernilai 1 sampai 5.',
            'system_rating.between' => 'Rating sistem harus bernilai 1 sampai 5.',
            'feedback.max' => 'Kritik dan saran maksimal 1000 karakter.',
        ];
    }
}
