<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    protected $table = 'tracking';

    protected $fillable = [
        'perizinan_id',
        'status',
        'keterangan',
        'user_id'
    ];

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
