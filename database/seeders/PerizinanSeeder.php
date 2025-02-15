<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perizinan;
use App\Models\User;

class PerizinanSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'pemohon')->get();
        
        foreach($users as $user) {
            for($i = 1; $i <= 3; $i++) {
                Perizinan::create([
                    'user_id' => $user->id,
                    'nomor' => 'IZN-' . date('YmdHis') . '-' . $user->id . '-' . $i,
                    'nama_pemohon' => 'Pemohon ' . $i,
                    'nik' => '1234567890123456',
                    'alamat' => 'Jl. Contoh No. ' . $i,
                    'nama_usaha' => 'Usaha ' . $i,
                    'jenis_usaha' => 'Perdagangan',
                    'alamat_usaha' => 'Jl. Usaha No. ' . $i,
                    'kategori' => 'HO',
                    'status' => 'PENDING',
                ]);
            }
        }
    }
}
