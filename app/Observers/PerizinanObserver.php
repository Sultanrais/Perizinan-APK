<?php

namespace App\Observers;

use App\Models\Perizinan;
use App\Models\Notification;
use App\Models\ActivityLog;

class PerizinanObserver
{
    /**
     * Handle the Perizinan "created" event.
     */
    public function created(Perizinan $perizinan): void
    {
        // Buat notifikasi untuk perizinan baru
        Notification::create([
            'perizinan_id' => $perizinan->id,
            'type' => 'perizinan_created',
            'title' => 'Perizinan Baru',
            'message' => "Perizinan baru dengan nomor {$perizinan->nomor_izin} telah dibuat.",
        ]);

        // Log aktivitas
        ActivityLog::create([
            'perizinan_id' => $perizinan->id,
            'activity_type' => 'perizinan_created',
            'description' => "Perizinan baru dengan nomor {$perizinan->nomor_izin} telah dibuat.",
            'performed_by' => 'System', // Nanti diganti dengan user yang login
        ]);
    }

    /**
     * Handle the Perizinan "updated" event.
     */
    public function updated(Perizinan $perizinan): void
    {
        // Jika status berubah
        if ($perizinan->isDirty('status')) {
            $oldStatus = $perizinan->getOriginal('status');
            $newStatus = $perizinan->status;

            // Buat notifikasi untuk perubahan status
            Notification::create([
                'perizinan_id' => $perizinan->id,
                'type' => 'status_changed',
                'title' => 'Status Perizinan Berubah',
                'message' => "Status perizinan {$perizinan->nomor_izin} telah berubah dari {$oldStatus} menjadi {$newStatus}.",
            ]);

            // Log aktivitas perubahan status
            ActivityLog::create([
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'status_changed',
                'description' => "Status perizinan berubah dari {$oldStatus} menjadi {$newStatus}.",
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $newStatus],
                'performed_by' => 'System', // Nanti diganti dengan user yang login
            ]);
        }
    }

    /**
     * Handle the Perizinan "deleted" event.
     */
    public function deleted(Perizinan $perizinan): void
    {
        // Log aktivitas penghapusan
        ActivityLog::create([
            'perizinan_id' => $perizinan->id,
            'activity_type' => 'perizinan_deleted',
            'description' => "Perizinan dengan nomor {$perizinan->nomor_izin} telah dihapus.",
            'performed_by' => 'System', // Nanti diganti dengan user yang login
        ]);
    }
}
