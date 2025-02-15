<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PerizinanExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Perizinan::query();

        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kategori
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Filter berdasarkan tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Hitung total per status
        $totalPending = Perizinan::where('status', 'PENDING')->count();
        $totalDisetujui = Perizinan::where('status', 'APPROVED')->count();
        $totalDitolak = Perizinan::where('status', 'REJECTED')->count();

        // Get data with pagination
        $perizinan = $query->latest()->paginate(10);

        return view('laporan.index', compact(
            'perizinan',
            'totalPending',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    public function perizinan(Request $request)
    {
        $query = Perizinan::with('user');

        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Filter berdasarkan kategori
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        $perizinan = $query->latest()->paginate(10);
        
        // Data untuk filter dropdown
        $kategoriList = Perizinan::select('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('laporan.perizinan', compact('perizinan', 'kategoriList'));
    }

    public function aktivitas(Request $request)
    {
        $query = ActivityLog::with(['user', 'perizinan']);

        // Filter berdasarkan tipe aktivitas
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Filter berdasarkan user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $activities = $query->latest()->paginate(10);

        return view('laporan.aktivitas', compact('activities'));
    }

    public function export(Request $request)
    {
        $query = Perizinan::query();

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $perizinan = $query->latest()->get();

        $format = $request->format ?? 'excel';
        $filename = 'laporan_perizinan_' . date('Y-m-d_His');

        if ($format === 'csv') {
            return Excel::download(new PerizinanExport($perizinan), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download(new PerizinanExport($perizinan), $filename . '.xlsx');
    }

    private function exportPerizinan(Request $request)
    {
        $query = Perizinan::with('user');

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $perizinan = $query->get();

        $filename = 'laporan_perizinan_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($handle, [
            'No',
            'Nomor Perizinan',
            'Pemohon',
            'Kategori',
            'Status',
            'Tanggal Pengajuan'
        ]);
        
        // Data
        foreach ($perizinan as $index => $izin) {
            fputcsv($handle, [
                $index + 1,
                $izin->nomor,
                $izin->user->name,
                $izin->kategori,
                $izin->status,
                $izin->created_at->format('d/m/Y')
            ]);
        }
        
        fclose($handle);
        exit;
    }

    private function exportAktivitas(Request $request)
    {
        $query = ActivityLog::with(['user', 'perizinan']);

        // Apply filters
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        $activities = $query->get();

        $filename = 'laporan_aktivitas_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($handle, [
            'No',
            'Tanggal',
            'User',
            'Aktivitas',
            'Detail'
        ]);
        
        // Data
        foreach ($activities as $index => $activity) {
            fputcsv($handle, [
                $index + 1,
                $activity->created_at->format('d/m/Y H:i'),
                $activity->user->name,
                $activity->title,
                $activity->message
            ]);
        }
        
        fclose($handle);
        exit;
    }
}
