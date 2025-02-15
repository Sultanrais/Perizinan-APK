<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'perizinan_id',
        'content',
        'commented_by',
        'comment_type',
        'is_private'
    ];

    protected $casts = [
        'is_private' => 'boolean'
    ];

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_private', true);
    }
}
