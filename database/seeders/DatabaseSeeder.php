<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perizinan;
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles first
        $adminRole = Role::create(['name' => 'admin']);
        $pemohonRole = Role::create(['name' => 'pemohon']);

        // Create admin user
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach($adminRole->id);

        // Create some sample users
        $users = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => "Pemohon $i",
                'email' => "pemohon$i@example.com",
                'password' => bcrypt('password'),
            ]);
            $user->roles()->attach($pemohonRole->id);
            $users[] = $user->id;
        }

        // Create sample perizinan
        $kategori = ['SIUP', 'IMB', 'SITU', 'TDP', 'HO'];
        $status = ['PENDING', 'APPROVED', 'REJECTED'];
        $counter = 1;
        
        // Create perizinan for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // Create 3-5 perizinan per month
            $count = rand(3, 5);
            for ($j = 0; $j < $count; $j++) {
                Perizinan::create([
                    'user_id' => $users[array_rand($users)],
                    'nomor' => 'P' . date('Ym', strtotime($date)) . str_pad($counter++, 3, '0', STR_PAD_LEFT),
                    'nama_pemohon' => 'Pemohon ' . rand(1, 100),
                    'nik' => '12345678' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT),
                    'alamat' => 'Jl. Pemohon No. ' . rand(1, 100),
                    'nama_usaha' => 'Usaha ' . rand(1, 100),
                    'jenis_usaha' => 'Jenis ' . rand(1, 10),
                    'alamat_usaha' => 'Jl. Contoh No. ' . rand(1, 100),
                    'kategori' => $kategori[array_rand($kategori)],
                    'status' => $status[array_rand($status)],
                    'created_at' => $date->copy()->addDays(rand(1, 28)),
                    'updated_at' => $date->copy()->addDays(rand(1, 28)),
                ]);
            }
        }
    }
}
