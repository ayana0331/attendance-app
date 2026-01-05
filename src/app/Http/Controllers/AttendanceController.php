<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\AttendanceCorrection;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->with('rests')
            ->first();

            $onBreak = false;
            if ($attendance) {
                $onBreak = $attendance->rests()
                    ->whereNull('break_end')
                    ->exists();
            }

        return view('index', compact('attendance', 'onBreak'));
    }

    public function start()
    {
        Attendance::create([
            'user_id'   => auth()->id(),
            'date' => now()->toDateString(),
            'clock_in' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function end()
    {
        $attendance = $this->todayRecord();
        $attendance->update(['clock_out' => now()]);

        return redirect()->route('attendance.index');
    }

    public function breakIn()
    {
        $attendance = $this->todayRecord();

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        return redirect()->route('attendance.index');
    }

    public function breakOut()
    {
        $attendance = $this->todayRecord();

        $rest = Rest::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if ($rest) {
            $rest->update(['break_end' => now()]);
        }
        return redirect()->route('attendance.index');
    }

    private function todayRecord()
    {
        return Attendance::where('user_id', auth()->id())
            ->whereDate('date', today())
            ->firstOrFail();
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        $month = Carbon::parse($request->get('month', Carbon::now()->format('Y-m')));
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::with('rests')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn ($a) => $a->date->format('Y-m-d'));

        $dates = [];
        for ($date = $startOfMonth->copy(); $date <= $endOfMonth; $date->addDay()) {
            $attendance = $attendances[$date->toDateString()] ?? null;

            $attendanceId = $attendance ? $attendance->id : $date->toDateString();

            $dates[] = [
                'date' => $date->copy(),
                'attendance' => $attendance,
                'attendanceId' => $attendanceId
            ];
        }

        return view('list', compact('dates', 'month'));
    }

    public function detail(string $id)
    {
        $user = auth()->user();
        $date = Carbon::createFromFormat('Y-m-d', $id);

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->first();

        $correction = AttendanceCorrection::where('user_id', $user->id)
            ->whereDate('date', $date)
            ->where('status', 'pending')
            ->first();

        return view('detail', compact('attendance','date','correction'));
    }
}