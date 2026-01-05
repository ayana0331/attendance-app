@extends('layouts.navigation')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">

    <h2 class="page-title">勤怠一覧</h2>

    <div class="month-nav">
        <a href="{{ route('attendance.list', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="month-link">
            <img src="{{ asset('images/arrow.png') }}" alt="前月">
            前月
        </a>
        <div class="month-current">
            <img src="{{ asset('images/calendar.png') }}" alt="カレンダー">
            {{ $month->format('Y/m') }}
        </div>
        <a href="{{ route('attendance.list', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="month-link">
            翌月
            <img src="{{ asset('images/arrow.png') }}" class="arrow-right" alt="翌月">
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($dates as $date)
            @php
                $attendance = $date['attendance'];
                $attendanceId = $date['attendanceId'];
            @endphp
            <tr>
                <td>{{ $date['date']->format('n/d') }} ({{ $date['date']->isoFormat('ddd') }})</td>
                <td>
                    @if ($attendance && $attendance->clock_in)
                        {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                    @else
                        <!-- 出勤データがない場合、空欄 -->
                    @endif
                </td>
                <td>
                    @if ($attendance && $attendance->clock_out)
                        {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                    @else
                        <!-- 退勤データがない場合、空欄 -->
                    @endif
                </td>
                <td>
                    @if ($attendance)
                        @php
                            $totalBreakTime = 0;
                            foreach ($attendance->rests as $rest) {
                                if ($rest->break_start && $rest->break_end) {
                                    $totalBreakTime += \Carbon\Carbon::parse($rest->break_start)->diffInMinutes(\Carbon\Carbon::parse($rest->break_end));
                                }
                            }
                            $hours = floor($totalBreakTime / 60);
                            $minutes = $totalBreakTime % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $hours, $minutes) }}
                    @else
                        <!-- 休憩データがない場合、空欄 -->
                    @endif
                </td>
                <td>
                    @if ($attendance && $attendance->clock_in && $attendance->clock_out)
                        @php
                            $clockIn = \Carbon\Carbon::parse($attendance->clock_in);
                            $clockOut = \Carbon\Carbon::parse($attendance->clock_out);
                            $workMinutes = $clockIn->diffInMinutes($clockOut);
                            $breakMinutes = 0;
                            foreach ($attendance->rests as $rest) {
                                if ($rest->break_start && $rest->break_end) {
                                    $breakMinutes += \Carbon\Carbon::parse($rest->break_start)->diffInMinutes(\Carbon\Carbon::parse($rest->break_end));
                                }
                            }
                            $totalMinutes = $workMinutes - $breakMinutes;
                            $hours = floor($totalMinutes / 60);
                            $minutes = $totalMinutes % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $hours, $minutes) }}
                    @else
                        <!-- 勤務データがない場合、空欄 -->
                    @endif
                </td>
                <td>
                    <a href="{{ route('attendance.detail', $date['date']->format('Y-m-d')) }}">詳細</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection