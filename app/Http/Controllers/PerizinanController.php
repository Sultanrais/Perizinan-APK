<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\Dokumen;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PerizinanController extends Controller
{
    public function index()
    {
        $query = Perizinan::with('user');

        // Jika user adalah pemohon, hanya tampilkan perizinan miliknya
        if (auth()->user()->hasRole('pemohon')) {
            $query->where('user_id', auth()->id());
        }

        $perizinan = $query->latest()->paginate(10);
        
        return view('perizinan.index', compact('perizinan'));
    }

    public function create()
    {
        $jenisUsaha = [
            'Perdagangan',
            'Jasa',
            'Manufaktur',
            'Konstruksi',
            'Pertanian',
            'Peternakan',
            'Perikanan',
            'Lainnya'
        ];

        $requiredDokumen = [
            'ktp' => 'Scan KTP Pemohon',
            'npwp' => 'NPWP',
            'foto_lokasi' => 'Foto Lokasi Usaha',
            'surat_pernyataan' => 'Surat Pernyataan',
            'proposal' => 'Proposal Usaha'
        ];

        return view('perizinan.create', compact('jenisUsaha', 'requiredDokumen'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_pemohon' => 'required|string|max:255',
                'nik' => 'required|string|size:16',
                'alamat' => 'required|string',
                'nama_usaha' => 'required|string|max:255',
                'jenis_usaha' => 'required|string|max:255',
                'alamat_usaha' => 'required|string',
                'kategori' => 'required|string|max:255',
                'konfirmasi' => 'required|accepted',
                'dokumen.ktp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'dokumen.npwp' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'dokumen.foto_lokasi' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'dokumen.surat_pernyataan' => 'required|file|mimes:pdf|max:2048',
                'dokumen.proposal' => 'required|file|mimes:pdf|max:2048',
            ], [
                'nama_pemohon.required' => 'Nama pemohon wajib diisi',
                'nik.required' => 'NIK wajib diisi',
                'nik.size' => 'NIK harus 16 digit',
                'alamat.required' => 'Alamat wajib diisi',
                'nama_usaha.required' => 'Nama usaha wajib diisi',
                'jenis_usaha.required' => 'Jenis usaha wajib diisi',
                'alamat_usaha.required' => 'Alamat usaha wajib diisi',
                'kategori.required' => 'Kategori perizinan wajib diisi',
                'konfirmasi.required' => 'Anda harus menyetujui pernyataan',
                'konfirmasi.accepted' => 'Anda harus menyetujui pernyataan',
                'dokumen.ktp.required' => 'Scan KTP wajib diunggah',
                'dokumen.npwp.required' => 'NPWP wajib diunggah',
                'dokumen.foto_lokasi.required' => 'Foto lokasi wajib diunggah',
                'dokumen.surat_pernyataan.required' => 'Surat pernyataan wajib diunggah',
                'dokumen.proposal.required' => 'Proposal usaha wajib diunggah',
                'dokumen.*.mimes' => 'Format file tidak sesuai',
                'dokumen.*.max' => 'Ukuran file maksimal 2MB'
            ]);

            \DB::beginTransaction();

            // Buat perizinan
            $perizinan = Perizinan::create([
                'user_id' => auth()->id(),
                'nama_pemohon' => $validated['nama_pemohon'],
                'nik' => $validated['nik'],
                'alamat' => $validated['alamat'],
                'nama_usaha' => $validated['nama_usaha'],
                'jenis_usaha' => $validated['jenis_usaha'],
                'alamat_usaha' => $validated['alamat_usaha'],
                'kategori' => $validated['kategori'],
                'status' => 'PENDING',
                'nomor' => 'IZN-' . date('YmdHis') . '-' . auth()->id(),
            ]);

            // Upload dan simpan dokumen
            foreach ($request->file('dokumen') as $type => $file) {
                Log::info('Processing file upload', [
                    'type' => $type,
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
                
                $path = $file->store('dokumen/' . $perizinan->id);
                
                Dokumen::create([
                    'perizinan_id' => $perizinan->id,
                    'jenis_dokumen' => $type,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                    'ekstensi' => $file->getClientOriginalExtension(),
                    'ukuran' => $file->getSize(),
                    'keterangan' => null
                ]);
            }

            // Tambah tracking
            $perizinan->trackings()->create([
                'user_id' => auth()->id(),
                'status' => 'PENDING',
                'keterangan' => 'Perizinan baru diajukan'
            ]);

            // Log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'created',
                'description' => 'Perizinan ' . $perizinan->nomor . ' telah diajukan',
                'performed_by' => auth()->user()->name
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.show', $perizinan->id)
                ->with('success', 'Perizinan berhasil diajukan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('current_step', 1);
        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('current_step', 1);
        }
    }

    public function show(Perizinan $perizinan)
    {
        $this->authorize('view', $perizinan);
        
        $perizinan->load(['user', 'dokumen', 'activityLogs.user', 'trackings.user']);
        
        return view('perizinan.show', compact('perizinan'));
    }

    public function updateStatus(Request $request, Perizinan $perizinan)
    {
        $this->authorize('updateStatus', $perizinan);

        $request->validate([
            'status' => 'required|in:PENDING,APPROVED,REJECTED',
            'keterangan' => 'required_if:status,REJECTED|string|nullable'
        ]);

        try {
            \DB::beginTransaction();

            $oldStatus = $perizinan->status;
            
            $perizinan->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan
            ]);

            // Tambah tracking
            $perizinan->trackings()->create([
                'user_id' => auth()->id(),
                'status' => $request->status,
                'keterangan' => $request->keterangan
            ]);

            // Log perubahan status
            ActivityLog::create([
                'user_id' => auth()->id(),
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'status_changed',
                'description' => 'Status perizinan ' . $perizinan->nomor . ' berubah dari ' . $oldStatus . ' menjadi ' . $request->status,
                'performed_by' => auth()->user()->name
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.show', $perizinan->id)
                ->with('success', 'Status perizinan berhasil diperbarui');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Perizinan $perizinan)
    {
        try {
            $this->authorize('delete', $perizinan);

            \DB::beginTransaction();

            // Simpan informasi perizinan untuk log
            $perizinanInfo = [
                'nomor' => $perizinan->nomor ?? 'tanpa nomor',
                'nama_usaha' => $perizinan->nama_usaha ?? '-',
                'status' => $perizinan->status ?? '-'
            ];

            // Hapus file dokumen dari storage
            foreach ($perizinan->dokumen as $dokumen) {
                if (Storage::exists('public/' . $dokumen->path)) {
                    Storage::delete('public/' . $dokumen->path);
                }
            }

            // Hapus perizinan (akan otomatis menghapus dokumen karena cascade)
            $perizinan->delete();

            // Buat log aktivitas
            \DB::table('activity_logs')->insert([
                'user_id' => auth()->id(),
                'activity_type' => 'perizinan_deleted',
                'description' => 'Perizinan ' . $perizinanInfo['nomor'] . ' telah dihapus',
                'old_values' => json_encode($perizinanInfo),
                'performed_by' => auth()->user()->name ?? 'System',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.index')
                ->with('success', 'Perizinan berhasil dihapus');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus perizinan: ' . $e->getMessage());
        }
    }

    public function edit(Perizinan $perizinan)
    {
        $this->authorize('update', $perizinan);

        $jenisUsaha = [
            'Perdagangan',
            'Jasa',
            'Manufaktur',
            'Konstruksi',
            'Pertanian',
            'Peternakan',
            'Perikanan',
            'Lainnya'
        ];

        return view('perizinan.edit', compact('perizinan', 'jenisUsaha'));
    }

    public function update(Request $request, Perizinan $perizinan)
    {
        $this->authorize('update', $perizinan);

        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'alamat' => 'required|string',
            'nama_usaha' => 'required|string|max:255',
            'jenis_usaha' => 'required|string|max:255',
            'alamat_usaha' => 'required|string',
            'kategori' => 'required|string|max:255',
        ]);

        try {
            \DB::beginTransaction();

            $oldValues = $perizinan->toArray();
            $perizinan->update($request->all());
            $newValues = $perizinan->fresh()->toArray();

            // Log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'updated',
                'description' => 'Perizinan ' . $perizinan->nomor . ' telah diperbarui',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'performed_by' => auth()->user()->name
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.show', $perizinan->id)
                ->with('success', 'Perizinan berhasil diperbarui');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function approve(Perizinan $perizinan)
    {
        $this->authorize('updateStatus', $perizinan);

        try {
            \DB::beginTransaction();

            $oldStatus = $perizinan->status;
            
            $perizinan->update([
                'status' => 'APPROVED',
                'keterangan' => 'Perizinan disetujui',
                'tanggal_disetujui' => now()
            ]);

            // Tambah tracking
            $perizinan->trackings()->create([
                'user_id' => auth()->id(),
                'status' => 'APPROVED',
                'keterangan' => 'Perizinan disetujui'
            ]);

            // Log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'status_changed',
                'description' => 'Status perizinan ' . $perizinan->nomor . ' berubah dari ' . $oldStatus . ' menjadi APPROVED',
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => 'APPROVED'],
                'performed_by' => auth()->user()->name
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.show', $perizinan->id)
                ->with('success', 'Perizinan berhasil disetujui');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Perizinan $perizinan)
    {
        $this->authorize('updateStatus', $perizinan);

        try {
            \DB::beginTransaction();

            $oldStatus = $perizinan->status;
            
            $perizinan->update([
                'status' => 'REJECTED',
                'keterangan' => 'Perizinan ditolak'
            ]);

            // Tambah tracking
            $perizinan->trackings()->create([
                'user_id' => auth()->id(),
                'status' => 'REJECTED',
                'keterangan' => 'Perizinan ditolak'
            ]);

            // Log aktivitas
            ActivityLog::create([
                'user_id' => auth()->id(),
                'perizinan_id' => $perizinan->id,
                'activity_type' => 'status_changed',
                'description' => 'Status perizinan ' . $perizinan->nomor . ' berubah dari ' . $oldStatus . ' menjadi REJECTED',
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => 'REJECTED'],
                'performed_by' => auth()->user()->name
            ]);

            \DB::commit();

            return redirect()
                ->route('perizinan.show', $perizinan->id)
                ->with('success', 'Perizinan berhasil ditolak');

        } catch (\Exception $e) {
            \DB::rollback();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function download(Perizinan $perizinan, Dokumen $dokumen)
    {
        // Pastikan user memiliki akses
        $this->authorize('view', $perizinan);
        
        // Pastikan dokumen milik perizinan yang dimaksud
        if ($dokumen->perizinan_id !== $perizinan->id) {
            abort(404);
        }

        // Pastikan file ada
        if (!Storage::exists($dokumen->path)) {
            return back()->with('error', 'File tidak ditemukan');
        }

        return Storage::download($dokumen->path, $dokumen->nama_file);
    }
}
