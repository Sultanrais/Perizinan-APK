<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Proses request
        $response = $next($request);

        // Log aktivitas jika route memiliki nama
        if ($request->route() && $request->route()->getName()) {
            $routeName = $request->route()->getName();
            
            // Tentukan tipe aktivitas berdasarkan route name
            $activityType = $this->determineActivityType($routeName);
            
            // Jika tipe aktivitas ditemukan, log aktivitas
            if ($activityType) {
                $this->logActivity($request, $activityType);
            }
        }

        return $response;
    }

    /**
     * Menentukan tipe aktivitas berdasarkan route name
     */
    private function determineActivityType(string $routeName): ?string
    {
        $types = [
            'perizinan.store' => 'perizinan_created',
            'perizinan.update' => 'perizinan_updated',
            'perizinan.destroy' => 'perizinan_deleted',
            'dokumen.store' => 'document_uploaded',
            'dokumen.destroy' => 'document_deleted',
            'comments.store' => 'comment_added',
            'comments.update' => 'comment_updated',
            'comments.destroy' => 'comment_deleted',
        ];

        return $types[$routeName] ?? null;
    }

    /**
     * Log aktivitas
     */
    private function logActivity(Request $request, string $activityType): void
    {
        // Dapatkan perizinan_id dari route parameter atau request
        $perizinanId = $request->route('perizinan') 
            ? $request->route('perizinan')->id 
            : ($request->input('perizinan_id') ?? null);

        if ($perizinanId) {
            ActivityLog::create([
                'perizinan_id' => $perizinanId,
                'activity_type' => $activityType,
                'description' => $this->getActivityDescription($activityType),
                'performed_by' => 'System', // Nanti diganti dengan user yang login
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
    }

    /**
     * Mendapatkan deskripsi aktivitas
     */
    private function getActivityDescription(string $activityType): string
    {
        $descriptions = [
            'perizinan_created' => 'Perizinan baru dibuat',
            'perizinan_updated' => 'Perizinan diperbarui',
            'perizinan_deleted' => 'Perizinan dihapus',
            'document_uploaded' => 'Dokumen baru diunggah',
            'document_deleted' => 'Dokumen dihapus',
            'comment_added' => 'Komentar baru ditambahkan',
            'comment_updated' => 'Komentar diperbarui',
            'comment_deleted' => 'Komentar dihapus',
        ];

        return $descriptions[$activityType] ?? 'Aktivitas tidak diketahui';
    }
}
