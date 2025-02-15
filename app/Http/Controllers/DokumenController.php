<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class DokumenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'perizinan_id' => 'required|exists:perizinan,id',
            'jenis_dokumen' => 'required|string',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // max 10MB
            'keterangan' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $perizinan = Perizinan::findOrFail($request->perizinan_id);
        
        // Hitung versi dokumen
        $version = Dokumen::where('perizinan_id', $perizinan->id)
            ->where('jenis_dokumen', $request->jenis_dokumen)
            ->max('version') + 1;

        // Generate nama file yang unik
        $extension = $file->getClientOriginalExtension();
        $fileName = $request->jenis_dokumen . '_' . time() . '.' . $extension;
        $filePath = 'perizinan/' . $perizinan->id . '/dokumen';

        // Jika file adalah gambar, kompres terlebih dahulu
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $image = Image::make($file);
            
            // Resize jika ukuran lebih dari 1200px
            if ($image->width() > 1200) {
                $image->resize(1200, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Kompres kualitas
            Storage::put($filePath . '/' . $fileName, (string) $image->encode(null, 80));
        } else {
            Storage::putFileAs($filePath, $file, $fileName);
        }

        // Simpan data dokumen
        $dokumen = new Dokumen([
            'perizinan_id' => $perizinan->id,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_dokumen' => $file->getClientOriginalName(),
            'file_path' => $filePath . '/' . $fileName,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'version' => $version,
            'keterangan' => $request->keterangan
        ]);

        $dokumen->save();

        return redirect()->back()->with('success', 'Dokumen berhasil diupload');
    }

    public function preview($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        if (!Storage::exists($dokumen->file_path)) {
            abort(404);
        }

        if ($dokumen->isPDF()) {
            return response()->file(
                Storage::path($dokumen->file_path),
                ['Content-Type' => 'application/pdf']
            );
        }

        return response()->file(
            Storage::path($dokumen->file_path),
            ['Content-Type' => $dokumen->file_type]
        );
    }

    public function download($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        if (!Storage::exists($dokumen->file_path)) {
            abort(404);
        }

        return Storage::download(
            $dokumen->file_path,
            $dokumen->nama_dokumen
        );
    }

    public function destroy($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Hapus file
        if (Storage::exists($dokumen->file_path)) {
            Storage::delete($dokumen->file_path);
        }

        $dokumen->delete();

        return response()->json(['message' => 'Dokumen berhasil dihapus']);
    }
}
