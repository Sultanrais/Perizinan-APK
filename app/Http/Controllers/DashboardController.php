<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalPerizinan = Perizinan::count();
        $totalPending = Perizinan::where('status', 'PENDING')->count();
        $totalApproved = Perizinan::where('status', 'APPROVED')->count();
        $totalRejected = Perizinan::where('status', 'REJECTED')->count();
        $totalUsers = User::count();

        // Get latest perizinan
        $latestPerizinan = Perizinan::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Get perizinan by month for chart
        $perizinanByMonth = Perizinan::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Prepare chart data
        $chartLabels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $chartData = array_fill(0, 12, 0); // Initialize with zeros
        
        foreach ($perizinanByMonth as $item) {
            $chartData[$item->month - 1] = $item->total;
        }

        // Get status distribution for pie chart
        $statusDistribution = [
            'Pending' => $totalPending,
            'Disetujui' => $totalApproved,
            'Ditolak' => $totalRejected
        ];

        // Get kategori distribution
        $kategoriDistribution = Perizinan::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get();

        // Jika user adalah pemohon, hanya tampilkan perizinan miliknya
        if (auth()->user()->hasRole('pemohon')) {
            $query = Perizinan::where('user_id', auth()->id());
            
            $totalPerizinan = $query->count();
            $totalPending = $query->where('status', 'PENDING')->count();
            $totalApproved = $query->where('status', 'APPROVED')->count();
            $totalRejected = $query->where('status', 'REJECTED')->count();
            
            $latestPerizinan = $query->with('user')
                ->latest()
                ->take(5)
                ->get();

            // Update kategori stats for user
            $kategoriDistribution = Perizinan::select('kategori', DB::raw('count(*) as total'))
                ->where('user_id', auth()->id())
                ->groupBy('kategori')
                ->get();
        }

        return view('dashboard.index', compact(
            'totalPerizinan',
            'totalPending',
            'totalApproved',
            'totalRejected',
            'totalUsers',
            'latestPerizinan',
            'chartLabels',
            'chartData',
            'statusDistribution',
            'kategoriDistribution'
        ));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $status = $request->input('status');

        $query = Perizinan::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $perizinan = $query->get();

        // Generate Excel file
        $filename = 'laporan_perizinan_' . date('Y-m-d') . '.xlsx';
        
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diexport',
            'data' => $perizinan
        ]);
    }

    public function filter(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;
        $status = $request->status;

        $query = Perizinan::query();

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $perizinan = $query->with('user')->latest()->get();

        return response()->json([
            'data' => view('dashboard._perizinan_table', compact('perizinan'))->render()
        ]);
    }
}
