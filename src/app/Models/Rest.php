<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
        'duration',
    ];

    protected $table = 'breaks';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($rest) {
            if ($rest->break_start && $rest->break_end) {
                $start = \Carbon\Carbon::parse($rest->break_start);
                $end = \Carbon\Carbon::parse($rest->break_end);
                $rest->duration = $start->diffInMinutes($end);
            }
        });
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
