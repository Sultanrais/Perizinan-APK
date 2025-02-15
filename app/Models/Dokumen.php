<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Dokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'perizinan_id',
        'jenis_dokumen',
        'nama_file',
        'path',
        'ekstensi',
        'ukuran',
        'keterangan'
    ];

    protected $appends = ['file_url', 'formatted_size'];

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class);
    }

    public function getFileUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->ukuran;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage()
    {
        return in_array($this->ekstensi, ['image/jpeg', 'image/png', 'image/gif']);
    }

    public function isPDF()
    {
        return $this->ekstensi === 'application/pdf';
    }
}
