<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Persyaratan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'wajib',
        'tipe_file',
        'max_size',
    ];

    protected $casts = [
        'wajib' => 'boolean',
        'max_size' => 'integer',
    ];

    public function perizinans(): BelongsToMany
    {
        return $this->belongsToMany(Perizinan::class, 'perizinan_persyaratan')
            ->withPivot(['file_path', 'status', 'catatan'])
            ->withTimestamps();
    }
}
