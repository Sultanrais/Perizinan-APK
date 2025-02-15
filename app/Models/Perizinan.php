<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Dokumen;
use App\Models\Notification;
use App\Models\ActivityLog;
use App\Models\Comment;
use App\Models\Persyaratan;
use App\Models\User;
use App\Models\Tracking;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'perizinans';

    protected $fillable = [
        'user_id',
        'nomor',
        'kategori',
        'nama_pemohon',
        'nik',
        'alamat',
        'nama_usaha',
        'jenis_usaha',
        'alamat_usaha',
        'status',
        'keterangan'
    ];

    protected $dates = [
        'tanggal_pengajuan',
        'tanggal_disetujui'
    ];

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function publicComments()
    {
        return $this->comments()->public();
    }

    public function internalComments()
    {
        return $this->comments()->internal();
    }

    public function getKtpDokumenAttribute()
    {
        return $this->dokumen()->where('jenis_dokumen', 'KTP')->first();
    }

    public function getSiupDokumenAttribute()
    {
        return $this->dokumen()->where('jenis_dokumen', 'SIUP')->first();
    }

    public function getBuktiPembayaranAttribute()
    {
        return $this->dokumen()->where('jenis_dokumen', 'Bukti Pembayaran')->first();
    }

    public function persyaratans(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Persyaratan::class, 'perizinan_persyaratan')
            ->withPivot(['file_path', 'status', 'catatan'])
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Log setiap perubahan status
        static::updated(function ($perizinan) {
            if ($perizinan->isDirty('status')) {
                ActivityLog::logActivity(
                    $perizinan->id,
                    'status_changed',
                    'Status perizinan berubah dari ' . $perizinan->getOriginal('status') . ' menjadi ' . $perizinan->status,
                    ['status' => $perizinan->getOriginal('status')],
                    ['status' => $perizinan->status]
                );

                // Buat notifikasi
                Notification::create([
                    'perizinan_id' => $perizinan->id,
                    'type' => 'status_changed',
                    'title' => 'Status Perizinan Berubah',
                    'message' => 'Status perizinan ' . $perizinan->nomor . ' telah berubah menjadi ' . $perizinan->status
                ]);
            }
        });

        static::creating(function ($perizinan) {
            if (!$perizinan->nomor) {
                $tahun = date('Y');
                $bulan = date('m');
                $count = static::whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->count();
                
                $perizinan->nomor = sprintf('IZN/%s/%s/%04d', $tahun, $bulan, $count + 1);
            }
        });
    }
}
