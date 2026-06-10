<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentUploadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'proof_file' => ['required','image','mimes:jpg,jpeg,png,webp,pdf','max:5120'],
            'amount'     => ['required','numeric','min:1'],
            'notes'      => ['nullable','string','max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_file.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_file.image'    => 'Bukti pembayaran harus berupa gambar.',
            'proof_file.max'      => 'Ukuran file maksimal 5MB.',
            'amount.required'     => 'Nominal pembayaran wajib diisi.',
        ];
    }
}
