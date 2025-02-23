<?php

namespace App\Http\Requests;

use App\Models\Channel;
use Illuminate\Foundation\Http\FormRequest;

class AntiSurveyRequest extends FormRequest
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
        $this->merge([
            'channelId' => Channel::query()->where('name', 'GT')->first()['id'],
        ]);
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
            'channelId' => ['required', 'exists:channels,id'],
            'reason' => ['required', 'max:100'],
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

            'channelId.required' => 'Channel tidak boleh kosong',
            'channelId.exists' => 'Channel tidak diketahui',

            'reason.required' => 'Alasan tidak boleh kosong',
            'reason.max' => 'Panjang alasan maksimal 100 karakter',
        ];
    }
}
