<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Perizinan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Perizinan $perizinan)
    {
        $request->validate([
            'content' => 'required|string',
            'is_private' => 'boolean',
            'comment_type' => 'required|in:internal,public'
        ]);

        $comment = $perizinan->comments()->create([
            'content' => $request->content,
            'commented_by' => 'System', // Nanti diganti dengan user yang login
            'is_private' => $request->is_private ?? true,
            'comment_type' => $request->comment_type
        ]);

        // Log aktivitas
        ActivityLog::logActivity(
            $perizinan->id,
            'comment_added',
            'Komentar baru ditambahkan pada perizinan ' . $perizinan->nomor_izin
        );

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        // Log aktivitas
        ActivityLog::logActivity(
            $comment->perizinan_id,
            'comment_updated',
            'Komentar diperbarui pada perizinan ' . $comment->perizinan->nomor_izin
        );

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui.');
    }

    public function destroy(Comment $comment)
    {
        $perizinanId = $comment->perizinan_id;
        $nomorIzin = $comment->perizinan->nomor_izin;
        
        $comment->delete();

        // Log aktivitas
        ActivityLog::logActivity(
            $perizinanId,
            'comment_deleted',
            'Komentar dihapus dari perizinan ' . $nomorIzin
        );

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }

    public function getComments(Perizinan $perizinan, Request $request)
    {
        $comments = $perizinan->comments()
            ->when($request->type === 'public', function($query) {
                return $query->public();
            })
            ->when($request->type === 'internal', function($query) {
                return $query->internal();
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }
}
