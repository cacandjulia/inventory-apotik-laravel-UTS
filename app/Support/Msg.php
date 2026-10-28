<?php

namespace App\Support;

/** Pesan validasi berbahasa Indonesia yang mudah dipahami. */
class Msg
{
    public static function id(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique' => ':attribute sudah dipakai, gunakan yang lain.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'numeric' => ':attribute harus berupa angka.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min.numeric' => ':attribute tidak boleh kurang dari :min.',
            'min.string' => ':attribute minimal :min karakter.',
            'min.array' => 'Minimal :min item harus diisi.',
            'max.string' => ':attribute maksimal :max karakter.',
            'email' => 'Format email tidak valid.',
            'in' => ':attribute yang dipilih tidak valid.',
        ];
    }
}
