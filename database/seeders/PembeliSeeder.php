<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PembeliSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate: aman dijalankan berulang (tidak membuat akun ganda)
        User::firstOrCreate(
            ['email' => 'acu@gmail.com'],
            ['name' => 'Pembeli Demo', 'password' => '87654321', 'role' => 'pembeli']
        );
    }
}
