<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAttendanceCorrectionController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        $query = AttendanceCorrection::with('user')->orderBy('created_at', 'desc');

        if ($tab === 'approved') {
            $query->where('status', 'approved');
        } else {
            $query->where('status', 'pending');
        }

        $items = $query->get();

        return view('admin.attendance_correction_list', compact('items', 'tab'));
    }

    public function approve(int $id)
    {
        $correction = AttendanceCorrection::findOrFail($id);

        DB::transaction(function () use ($correction) {
            $attendance = Attendance::firstOrCreate(
                [
                    'user_id' => $correction->user_id,
                    'date'    => $correction->date,
                ]
            );

            $attendance->update([
                'clock_in'  => $correction->clock_in,
                'clock_out' => $correction->clock_out,
            ]);

            $attendance->rests()->delete();

            if ($correction->break_start && $correction->break_end) {
                $attendance->rests()->create([
                    'break_start' => $correction->break_start,
                    'break_end'   => $correction->break_end,
                ]);
            }

            if ($correction->break2_start && $correction->break2_end) {
                $attendance->rests()->create([
                    'break2_start' => $correction->break2_start,
                    'break2_end'   => $correction->break2_end,
                ]);
            }

            $correction->update([
                'status' => 'approved',
            ]);
        });

        return redirect()->route('admin.attendance.detail', ['user' => $correction->user_id, 'date' => $correction->date,])->with(['just_approved' => true, 'prev_reason'   => $correction->reason]);
    }
}