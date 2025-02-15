<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Buat role jika belum ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $pemohonRole = Role::firstOrCreate(['name' => 'pemohon']);

        // Buat user admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        $admin->roles()->attach($adminRole);

        // Buat user pemohon
        $pemohon = User::create([
            'name' => 'Pemohon',
            'email' => 'pemohon@example.com',
            'password' => Hash::make('password'),
            'role' => 'pemohon'
        ]);
        $pemohon->roles()->attach($pemohonRole);
    }
}
