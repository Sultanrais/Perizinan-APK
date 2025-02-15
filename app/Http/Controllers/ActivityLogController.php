<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Perizinan;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['perizinan', 'user'])->orderBy('created_at', 'desc');

        // Filter berdasarkan tipe aktivitas
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter berdasarkan perizinan
        if ($request->filled('perizinan_id')) {
            $query->where('perizinan_id', $request->perizinan_id);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $activityLogs = $query->paginate(15);

        return view('activity-logs.index', compact('activityLogs'));
    }

    public function perizinanLogs(Perizinan $perizinan)
    {
        $activityLogs = $perizinan->activityLogs()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('activity-logs.perizinan', compact('perizinan', 'activityLogs'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with(['perizinan', 'user']);

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('perizinan_id')) {
            $query->where('perizinan_id', $request->perizinan_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $logs = $query->get();

        // Format data untuk export
        $exportData = $logs->map(function($log) {
            return [
                'Tanggal' => $log->created_at->format('d/m/Y H:i:s'),
                'Nomor Izin' => $log->perizinan->nomor_izin,
                'Tipe Aktivitas' => $log->activity_type,
                'Deskripsi' => $log->description,
                'Dilakukan Oleh' => $log->performed_by,
                'IP Address' => $log->ip_address
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }
}
