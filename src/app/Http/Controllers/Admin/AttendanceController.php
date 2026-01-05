<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Rest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $attendances = Attendance::with(['user', 'rests'])
            ->whereDate('date', $date)
            ->get();

        return view('admin.list', [
            'date' => $date,
            'attendances' => $attendances,
        ]);
    }

    public function detail(User $user, string $date)
    {
        $date = Carbon::parse($date);

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        $correction = AttendanceCorrection::where('user_id', $user->id)
            ->whereDate('date', $date->toDateString())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('admin.detail', [
            'attendance' => $attendance,
            'correction' => $correction,
            'date'       => $date,
            'user'       => $user,
            'isAdmin'    => true,
        ]);
    }

    public function update(AttendanceUpdateRequest $request)
    {
        $userId = $request->input('user_id');
        $date = $request->input('date');

        DB::transaction(function () use ($request, $userId, $date) {
            $attendance = Attendance::firstOrCreate(
                ['user_id' => $userId, 'date' => $date]
            );

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
            ]);

            $attendance->rests()->delete();

            if ($request->break_start || $request->break_end) {
                Rest::create([
                    'attendance_id' => $attendance->id,
                    'break_start'   => $request->break_start,
                    'break_end'     => $request->break_end,
                ]);
            }

            if ($request->break2_start || $request->break2_end) {
                Rest::create([
                    'attendance_id' => $attendance->id,
                    'break_start'   => $request->break2_start,
                    'break_end'     => $request->break2_end,
                ]);
            }
        });

        return redirect()->back();
    }
}
