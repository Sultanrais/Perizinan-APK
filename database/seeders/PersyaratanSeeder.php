<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persyaratan;

class PersyaratanSeeder extends Seeder
{
    public function run(): void
    {
        $persyaratans = [
            [
                'nama' => 'KTP',
                'deskripsi' => 'Kartu Tanda Penduduk yang masih berlaku',
                'wajib' => true,
                'tipe_file' => 'image,pdf',
                'max_size' => 2048, // 2MB
            ],
            [
                'nama' => 'KK',
                'deskripsi' => 'Kartu Keluarga yang masih berlaku',
                'wajib' => true,
                'tipe_file' => 'image,pdf',
                'max_size' => 2048,
            ],
            [
                'nama' => 'Pas Foto',
                'deskripsi' => 'Pas foto terbaru ukuran 3x4 dengan latar belakang merah',
                'wajib' => true,
                'tipe_file' => 'image',
                'max_size' => 1024, // 1MB
            ],
            [
                'nama' => 'Surat Pernyataan',
                'deskripsi' => 'Surat pernyataan bermaterai yang menyatakan data yang diisi adalah benar',
                'wajib' => true,
                'tipe_file' => 'pdf',
                'max_size' => 5120, // 5MB
            ],
        ];

        foreach ($persyaratans as $persyaratan) {
            Persyaratan::create($persyaratan);
        }
    }
}
