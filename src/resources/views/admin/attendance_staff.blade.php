@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">

    <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>

    <div class="month-nav">
        <a href="{{ route('admin.attendance.staff', ['user' => $user->id, 'month' => $month->copy()->subMonth()->format('Y-m')]) }}" class="month-link">
            <img src="{{ asset('images/arrow.png') }}" alt="前月">
            前月
        </a>
        <div class="month-current">
            <img src="{{ asset('images/calendar.png') }}" alt="カレンダー">
            {{ $month->format('Y/m') }}
        </div>
        <a href="{{ route('admin.attendance.staff', ['user' => $user->id, 'month' => $month->copy()->addMonth()->format('Y-m')]) }}" class="month-link">
            翌月
            <img src="{{ asset('images/arrow.png') }}" class="arrow-right" alt="翌月">
        </a>
    </div>

    {{-- 一覧 --}}
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
        @foreach ($dates as $item)
            @php
                $attendance = $item['attendance'] ?? (isset($attendances) ? $attendances->first(function($a) use ($item) {
                    return \Carbon\Carbon::parse($a->date)->toDateString() == $item['date']->toDateString();
                }) : null);
            @endphp
            <tr>
                <td>
                    {{ $item['date']->format('n/d') }}
                    ({{ $item['date']->isoFormat('ddd') }})
                </td>

                <td>
                    @if ($attendance && $attendance->clock_in)
                        {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                    @endif
                </td>

                <td>
                    @if ($attendance && $attendance->clock_out)
                        {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                    @endif
                </td>

                <td>
                    @if ($attendance)
                        @php
                            $breakMinutes = 0;
                            foreach ($attendance->rests as $rest) {
                                if ($rest->break_start && $rest->break_end) {
                                    $breakMinutes +=
                                        \Carbon\Carbon::parse($rest->break_start)
                                            ->diffInMinutes($rest->break_end);
                                }
                            }
                            $h = intdiv($breakMinutes, 60);
                            $m = $breakMinutes % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $h, $m) }}
                    @endif
                </td>

                <td>
                    @if ($attendance && $attendance->clock_in && $attendance->clock_out)
                        @php
                            $workMinutes =
                                \Carbon\Carbon::parse($attendance->clock_in)
                                    ->diffInMinutes($attendance->clock_out)
                                - $breakMinutes;

                            $h = intdiv($workMinutes, 60);
                            $m = $workMinutes % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $h, $m) }}
                    @endif
                </td>

                <td>
                    <a href="{{ route('admin.attendance.detail', ['user' => $user->id, 'date' => $item['date']->toDateString()]) }}">
                        詳細
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="csv-button-container">
        <a href="{{ route('admin.attendance.staff.export', ['user' => $user->id, 'month' => $month->format('Y-m')]) }}" class="btn btn-csv">
            CSV出力
        </a>
    </div>
</div>
@endsection