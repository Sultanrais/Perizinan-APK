<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Perizinan;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'activity_type',
        'description',
        'old_values',
        'new_values',
        'performed_by',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perizinan()
    {
        return $this->belongsTo(Perizinan::class);
    }

    public static function logActivity($perizinanId, $type, $description, $oldValues = null, $newValues = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'perizinan_id' => $perizinanId,
            'activity_type' => $type,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'performed_by' => auth()->user()->name ?? 'System',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
