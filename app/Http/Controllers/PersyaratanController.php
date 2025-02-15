<?php

namespace App\Http\Controllers;

use App\Models\Persyaratan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PersyaratanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['index', 'show']);
    }

    public function index()
    {
        $persyaratans = Persyaratan::all();
        return view('persyaratan.index', compact('persyaratans'));
    }

    public function create()
    {
        return view('persyaratan.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'wajib' => 'required|boolean',
            'tipe_file' => 'required|string',
            'max_size' => 'required|integer|min:1|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Persyaratan::create($request->all());

        return redirect()->route('persyaratan.index')
            ->with('success', 'Persyaratan berhasil ditambahkan');
    }

    public function show(Persyaratan $persyaratan)
    {
        return view('persyaratan.show', compact('persyaratan'));
    }

    public function edit(Persyaratan $persyaratan)
    {
        return view('persyaratan.edit', compact('persyaratan'));
    }

    public function update(Request $request, Persyaratan $persyaratan)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'wajib' => 'required|boolean',
            'tipe_file' => 'required|string',
            'max_size' => 'required|integer|min:1|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $persyaratan->update($request->all());

        return redirect()->route('persyaratan.index')
            ->with('success', 'Persyaratan berhasil diperbarui');
    }

    public function destroy(Persyaratan $persyaratan)
    {
        // Hapus file yang terkait dengan persyaratan ini
        $perizinanPersyaratans = $persyaratan->perizinans()->withPivot('file_path')->get();
        foreach ($perizinanPersyaratans as $perizinan) {
            if ($perizinan->pivot->file_path) {
                Storage::delete($perizinan->pivot->file_path);
            }
        }

        $persyaratan->delete();

        return redirect()->route('persyaratan.index')
            ->with('success', 'Persyaratan berhasil dihapus');
    }

    public function uploadFile(Request $request, $perizinanId, $persyaratanId)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $persyaratan = Persyaratan::findOrFail($persyaratanId);
        $allowedTypes = explode(',', $persyaratan->tipe_file);
        $fileExtension = $request->file('file')->getClientOriginalExtension();

        if (!in_array($fileExtension, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe file tidak diizinkan. Tipe yang diizinkan: ' . $persyaratan->tipe_file
            ], 422);
        }

        if ($request->file('file')->getSize() > ($persyaratan->max_size * 1024)) {
            return response()->json([
                'success' => false,
                'message' => 'Ukuran file melebihi batas maksimal: ' . ($persyaratan->max_size / 1024) . 'MB'
            ], 422);
        }

        $path = $request->file('file')->store('public/persyaratan');

        $perizinan = Perizinan::findOrFail($perizinanId);
        $perizinan->persyaratans()->updateExistingPivot($persyaratanId, [
            'file_path' => $path,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diunggah',
            'path' => $path
        ]);
    }
}
