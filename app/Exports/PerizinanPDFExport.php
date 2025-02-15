<?php

namespace App\Exports;

use App\Models\Perizinan;
use Dompdf\Dompdf;
use Carbon\Carbon;

class PerizinanPDFExport
{
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $userId;

    public function __construct($startDate = null, $endDate = null, $status = null, $userId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->userId = $userId;
    }

    public function download()
    {
        $query = Perizinan::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_pengajuan', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay()
            ]);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }

        $perizinans = $query->with('user')->get();

        $html = view('exports.perizinan-pdf', [
            'perizinans' => $perizinans,
            'startDate' => $this->startDate ? Carbon::parse($this->startDate)->format('d/m/Y') : '-',
            'endDate' => $this->endDate ? Carbon::parse($this->endDate)->format('d/m/Y') : '-',
            'status' => $this->status ? ucfirst($this->status) : 'Semua',
            'tanggal_cetak' => now()->format('d/m/Y H:i')
        ])->render();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $dompdf->stream('laporan-perizinan.pdf');
    }
}
