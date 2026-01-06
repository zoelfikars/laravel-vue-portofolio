<?php

namespace App\Modules\UserProfile\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');
        $currentUser = $this->user();
        return $currentUser->hasRole('super-admin') || $currentUser->id === $targetUser->id;
    }
    public function rules()
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'work_interest' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'cv' => ['nullable', 'mimes:pdf', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
    public function messages()
    {
        return [
            'full_name.required' => 'Nama lengkap wajib diisi',
            'full_name.max' => 'Nama lengkap maksimal 255 karakter',
            'place_of_birth.required' => 'Tempat lahir wajib diisi',
            'place_of_birth.max' => 'Tempat lahir maksimal 255 karakter',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi',
            'date_of_birth.date' => 'Tanggal lahir harus berupa tanggal',
            'work_interest.required' => 'Bidang kerja wajib diisi',
            'work_interest.max' => 'Bidang kerja maksimal 255 karakter',
            'summary.required' => 'Ringkasan wajib diisi',
            'photo.image' => 'Foto harus berupa gambar',
            'photo.max' => 'Foto maksimal 2MB',
            'cv.mimes' => 'CV harus berupa PDF',
            'cv.max' => 'CV maksimal 5MB',
            'is_active.boolean' => 'Format status tidak valid (true/false)',
        ];
    }
}
