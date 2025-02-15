<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : now();
        
        $perizinan = Perizinan::whereDate('created_at', $date->format('Y-m-d'))
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $data = [
            'date' => $date->format('Y-m-d'),
            'total' => [
                'all' => Perizinan::whereDate('created_at', $date->format('Y-m-d'))->count(),
                'pending' => $perizinan->where('status', 'pending')->first()?->total ?? 0,
                'disetujui' => $perizinan->where('status', 'disetujui')->first()?->total ?? 0,
                'ditolak' => $perizinan->where('status', 'ditolak')->first()?->total ?? 0,
            ]
        ];

        return view('reports.daily', compact('data'));
    }

    public function monthly(Request $request)
    {
        $date = $request->input('month') ? Carbon::createFromFormat('Y-m', $request->input('month')) : now();
        $year = $date->year;
        $month = $date->month;
        
        $perizinan = Perizinan::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('date', 'status')
            ->get();

        $dailyData = collect();
        $currentDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        while ($currentDate <= $endDate) {
            $currentDateStr = $currentDate->format('Y-m-d');
            $dailyData[$currentDateStr] = [
                'pending' => $perizinan->where('date', $currentDateStr)->where('status', 'pending')->first()?->total ?? 0,
                'disetujui' => $perizinan->where('date', $currentDateStr)->where('status', 'disetujui')->first()?->total ?? 0,
                'ditolak' => $perizinan->where('date', $currentDateStr)->where('status', 'ditolak')->first()?->total ?? 0,
            ];
            $currentDate->addDay();
        }

        $data = [
            'month' => $date->format('Y-m'),
            'daily_data' => $dailyData,
            'total' => [
                'all' => $perizinan->sum('total'),
                'pending' => $perizinan->where('status', 'pending')->sum('total'),
                'disetujui' => $perizinan->where('status', 'disetujui')->sum('total'),
                'ditolak' => $perizinan->where('status', 'ditolak')->sum('total'),
            ]
        ];

        return view('reports.monthly', compact('data'));
    }

    public function yearly(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $perizinan = Perizinan::whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('month', 'status')
            ->get();

        $monthlyData = collect();
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = [
                'pending' => $perizinan->where('month', $month)->where('status', 'pending')->first()?->total ?? 0,
                'disetujui' => $perizinan->where('month', $month)->where('status', 'disetujui')->first()?->total ?? 0,
                'ditolak' => $perizinan->where('month', $month)->where('status', 'ditolak')->first()?->total ?? 0,
            ];
        }

        $data = [
            'year' => $year,
            'monthly_data' => $monthlyData,
            'total' => [
                'all' => $perizinan->sum('total'),
                'pending' => $perizinan->where('status', 'pending')->sum('total'),
                'disetujui' => $perizinan->where('status', 'disetujui')->sum('total'),
                'ditolak' => $perizinan->where('status', 'ditolak')->sum('total'),
            ]
        ];

        return view('reports.yearly', compact('data'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'daily');
        
        switch ($type) {
            case 'daily':
                $date = Carbon::parse($request->input('date', now()))->format('Y-m-d');
                return $this->exportDaily($date);
            case 'monthly':
                $month = $request->input('month', now()->format('Y-m'));
                return $this->exportMonthly($month);
            case 'yearly':
                $year = $request->input('year', now()->year);
                return $this->exportYearly($year);
            default:
                return redirect()->back()->with('error', 'Tipe laporan tidak valid');
        }
    }

    private function exportDaily($date)
    {
        $perizinan = Perizinan::whereDate('created_at', $date)
            ->with(['dokumen', 'activityLogs'])
            ->get()
            ->map(function ($item) {
                return [
                    'Nomor Izin' => $item->nomor_izin,
                    'Nama Pemohon' => $item->nama_pemohon,
                    'Status' => $item->status,
                    'Tanggal Pengajuan' => Carbon::parse($item->created_at)->format('d/m/Y H:i:s'),
                    'Jumlah Dokumen' => $item->dokumen->count(),
                    'Aktivitas Terakhir' => $item->activityLogs->last()?->description ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $perizinan
        ]);
    }

    private function exportMonthly($month)
    {
        $date = Carbon::createFromFormat('Y-m', $month);
        
        $perizinan = Perizinan::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with(['dokumen', 'activityLogs'])
            ->get()
            ->map(function ($item) {
                return [
                    'Nomor Izin' => $item->nomor_izin,
                    'Nama Pemohon' => $item->nama_pemohon,
                    'Status' => $item->status,
                    'Tanggal Pengajuan' => Carbon::parse($item->created_at)->format('d/m/Y H:i:s'),
                    'Jumlah Dokumen' => $item->dokumen->count(),
                    'Aktivitas Terakhir' => $item->activityLogs->last()?->description ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $perizinan
        ]);
    }

    private function exportYearly($year)
    {
        $perizinan = Perizinan::whereYear('created_at', $year)
            ->with(['dokumen', 'activityLogs'])
            ->get()
            ->map(function ($item) {
                return [
                    'Nomor Izin' => $item->nomor_izin,
                    'Nama Pemohon' => $item->nama_pemohon,
                    'Status' => $item->status,
                    'Tanggal Pengajuan' => Carbon::parse($item->created_at)->format('d/m/Y H:i:s'),
                    'Jumlah Dokumen' => $item->dokumen->count(),
                    'Aktivitas Terakhir' => $item->activityLogs->last()?->description ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $perizinan
        ]);
    }
}
