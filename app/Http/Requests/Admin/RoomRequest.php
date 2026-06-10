<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'number'       => ['required','string','max:20'],
            'floor'        => ['required','integer','min:1','max:50'],
            'price'        => ['required','numeric','min:0'],
            'status'       => ['required','in:available,occupied,maintenance'],
            'description'  => ['nullable','string','max:1000'],
            'photos'       => ['nullable','array'],
            'photos.*'     => ['image','mimes:jpg,jpeg,png,webp','max:2048'],
            'facilities'   => ['nullable','array'],
            'facilities.*' => ['exists:facilities,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Nomor kamar wajib diisi.',
            'floor.required'  => 'Lantai wajib diisi.',
            'price.required'  => 'Harga wajib diisi.',
            'price.numeric'   => 'Harga harus berupa angka.',
            'status.required' => 'Status wajib dipilih.',
        ];
    }
}
