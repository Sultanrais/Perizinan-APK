<?php

namespace App\Exports;

use App\Models\Perizinan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PerizinanExport implements FromCollection, WithHeadings, WithMapping
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

    public function collection()
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

        return $query->with('user')->get();
    }

    public function headings(): array
    {
        return [
            'No. Izin',
            'Nama Pemohon',
            'NIK',
            'Jenis Usaha',
            'Alamat Usaha',
            'Status',
            'Tanggal Pengajuan',
            'Tanggal Update',
            'Keterangan'
        ];
    }

    public function map($perizinan): array
    {
        return [
            $perizinan->nomor_izin,
            $perizinan->nama_pemohon,
            $perizinan->nik,
            $perizinan->jenis_usaha,
            $perizinan->alamat_usaha,
            ucfirst($perizinan->status),
            $perizinan->tanggal_pengajuan ? Carbon::parse($perizinan->tanggal_pengajuan)->format('d/m/Y') : '-',
            $perizinan->updated_at->format('d/m/Y H:i'),
            $perizinan->keterangan ?? '-'
        ];
    }
}
