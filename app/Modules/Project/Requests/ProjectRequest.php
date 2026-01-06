<?php

namespace App\Modules\Project\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string|max:500',
            'content' => 'required_if:published_at,!=,null|nullable|string',
            'demo_url' => 'nullable|url',
            'repository_url' => 'nullable|url',
            'published_at' => 'nullable|date',
            'tech_stack' => 'nullable|array',
            'tech_stack.*' => 'string|distinct'
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Judul wajib diisi.',
            'title.string' => 'Judul harus berupa string.',
            'title.max' => 'Judul tidak boleh lebih dari 255 karakter.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Thumbnail harus berupa gambar dengan format jpeg, png, jpg, atau webp.',
            'thumbnail.max' => 'Ukuran thumbnail tidak boleh lebih dari 2MB.',
            'description.string' => 'Deskripsi harus berupa string.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 500 karakter.',
            'content.required_if' => 'Konten wajib diisi jika proyek dipublikasikan.',
            'demo_url.url' => 'URL demo tidak valid.',
            'repository_url.url' => 'URL repositori tidak valid.',
            'published_at.date' => 'Format tanggal tidak valid.',
            'tech_stack.array' => 'Tech stack harus berupa array.',
            'tech_stack.*.string' => 'Setiap tech stack harus berupa string.',
            'tech_stack.*.distinct' => 'Terdapat tech stack yang duplikat.'
        ];
    }
}
