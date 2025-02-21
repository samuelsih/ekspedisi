<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyWithoutChannelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->has('questions')) {
            $this->merge([
                'questions' => json_decode($this->questions, true),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customerId' => ['required'],
            'driverId' => ['required', 'exists:drivers,id'],
            'questions' => ['required', 'array'],
            'image' => ['required', 'image'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'customerId.required' => 'ID Customer tidak boleh kosong',
            'customerId.exists' => 'ID Customer tidak diketahui',

            'driverId.required' => 'NIK Driver tidak boleh kosong',
            'driverId.exists' => 'NIK Driver tidak diketahui',

            'questions.required' => 'Pertanyaan wajib diisi',
            'questions.array' => 'Bentuk pertanyaan tidak valid',

            'image.required' => 'Tidak ada tangkapan layar',
            'image.image' => 'Harus berupa gambar',
        ];
    }
}
