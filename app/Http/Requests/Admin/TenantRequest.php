<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TenantRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_id'    => ['required','exists:rooms,id'],
            'name'       => ['required','string','max:255'],
            'email'      => ['nullable','email','max:255'],
            'phone'      => ['required','string','max:20'],
            'gender'     => ['required','in:male,female'],
            'address'    => ['nullable','string','max:1000'],
            'id_card'    => ['nullable','string','max:50'],
            'photo'      => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'start_date' => ['required','date'],
            'end_date'   => ['nullable','date','after:start_date'],
            'status'     => ['required','in:active,inactive'],
            'notes'      => ['nullable','string','max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required'    => 'Kamar wajib dipilih.',
            'room_id.exists'      => 'Kamar tidak ditemukan.',
            'name.required'       => 'Nama wajib diisi.',
            'phone.required'      => 'Nomor HP wajib diisi.',
            'gender.required'     => 'Jenis kelamin wajib dipilih.',
            'start_date.required' => 'Tanggal masuk wajib diisi.',
        ];
    }
}
