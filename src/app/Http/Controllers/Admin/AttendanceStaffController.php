<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceStaffController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('admin.staff_list', compact('users'));
    }

    public function show(User $user, Request $request)
    {
        $user = User::findOrFail($user->id);
        $month = Carbon::parse($request->get('month', now()->format('Y-m')));
        $start = $month->copy()->startOfMonth();
        $end   = $month->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->with('rests')
            ->get();

        $dates = [];
        for ($d = $start->copy(); $d <= $end; $d->addDay()) {
            $dates[] = [
                'date' => $d->copy(),
                'attendance' => $attendances->firstWhere('date', $d->toDateString()),
            ];
        }

        return view('admin.attendance_staff', compact('user','dates','month','attendances'));
    }

    public function exportCsv(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $monthStr = $request->get('month', now()->format('Y-m'));
        $month = \Carbon\Carbon::parse($monthStr);
        $start = $month->copy()->startOfMonth()->toDateString();
        $end   = $month->copy()->endOfMonth()->toDateString();

        $attendances = Attendance::where('user_id', $user_id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'asc')
            ->with('rests')
            ->get();

        $response = new StreamedResponse(function () use ($user, $attendances) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['名前', '日付', '出勤', '退勤', '休憩1開始', '休憩1終了', '休憩2開始', '休憩2終了']);

            foreach ($attendances as $attendance) {
                $rest1 = $attendance->rests->get(0);
                $rest2 = $attendance->rests->get(1);

                fputcsv($handle, [
                    $user->name,
                    $attendance->date,
                    $attendance->clock_in,
                    $attendance->clock_out,
                    $rest1 ? $rest1->break_start : '',
                    $rest1 ? $rest1->break_end : '',
                    $rest2 ? $rest2->break_start : '',
                    $rest2 ? $rest2->break_end : '',
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename={$user->name}_{$month}_attendance.csv");

        return $response;
    }
}
