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
            ['email' => 'pembeli@apotik.test'],
            ['name' => 'Pembeli Demo', 'password' => 'pembeli12345', 'role' => 'pembeli']
        );
    }
}
