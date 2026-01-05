<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'total_minutes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->rests->sum(function ($rest) {
            if (!$rest->break_start || !$rest->break_end) {
                return 0;
            }

            return Carbon::parse($rest->break_end)
                ->diffInMinutes(Carbon::parse($rest->break_start));
        });
    }

    public function corrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }
}
