<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use App\Http\Requests\AttendanceUpdateRequest;

class AttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();
        $tab = $request->query('tab', 'pending');

        $pending = AttendanceCorrection::where('user_id', $userId)
            ->where('status', 'pending')
            ->orderBy('date', 'desc')
            ->get();

        $approved = AttendanceCorrection::where('user_id', $userId)
            ->where('status', 'approved')
            ->orderBy('date', 'desc')
            ->get();

        $items = $tab === 'approved' ? $approved : $pending;

        return view('attendance_correction_list', compact('tab', 'items'));
    }

    public function store(AttendanceUpdateRequest $request)
    {
        $userId = auth()->id();

        $exists = AttendanceCorrection::where('user_id', $userId)
            ->where('date', $request->date)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'date' => 'この日はすでに修正申請中です。',
            ])->withInput();
        }

        AttendanceCorrection::create([
            'user_id' => $userId,
            'attendance_id' => $request->attendance_id,
            'date' => $request->date,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'break_start' => $request->break_start,
            'break_end' => $request->break_end,
            'break2_start' => $request->break2_start,
            'break2_end' => $request->break2_end,
            'reason' => $request->remarks,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.detail', $request->date);
    }
}
