<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        $start = Carbon::create(2025, 6, 1);
        $end = Carbon::create(2025, 12, 31);

        foreach ($users as $user) {
            $date = $start->copy();

            while ($date < $end) {
                if (!$date->isWeekend()) {
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date->toDateString(),
                        'clock_in' => '09:00',
                        'clock_out' => '18:00',
                        'total_minutes' => 8 * 60,
                    ]);

                    Rest::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => '12:00',
                        'break_end' => '13:00',
                    ]);
                }

                $date->addDay();
            }
        }
    }
}
