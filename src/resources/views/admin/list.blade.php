@extends('layouts.admin')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h2 class="page-title">
        {{ $date->format('Y年n月j日') }} の勤怠
    </h2>

    <div class="month-nav">
        <a href="{{ route('admin.attendance.list', ['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="month-link">
            <img src="{{ asset('images/arrow.png') }}" alt="前日">
            前日
        </a>
        <div class="month-current">
            <img src="{{ asset('images/calendar.png') }}" alt="カレンダー">
            {{ $date->format('Y/m/d') }}
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="month-link">
            翌日
            <img src="{{ asset('images/arrow.png') }}" class="arrow-right" alt="翌日">
        </a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @if ($attendances->isEmpty())
                <tr>
                    <td colspan="6" class="no-attendance">
                        出勤者がいません
                    </td>
                </tr>
            @else
                @foreach ($attendances as $attendance)
                    @php
                // 休憩合計（分）
                        $breakMinutes = $attendance->rests->sum(function ($rest) {
                            if ($rest->break_start && $rest->break_end) {
                                return \Carbon\Carbon::parse($rest->break_start)
                                    ->diffInMinutes($rest->break_end);
                            }
                            return 0;
                        });

                        $breakHours = floor($breakMinutes / 60);
                        $breakMins  = $breakMinutes % 60;
                    @endphp

                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>
                            @if ($attendance->clock_in)
                                {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                            @endif
                        </td>
                        <td>
                            @if ($attendance->clock_out)
                                {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                            @endif
                        </td>
                        <td>{{ sprintf('%d:%02d', $breakHours, $breakMins) }}</td>
                        <td>
                            @if ($attendance->clock_in && $attendance->clock_out)
                                @php
                                    $workMinutes =
                                        \Carbon\Carbon::parse($attendance->clock_in)
                                            ->diffInMinutes($attendance->clock_out)
                                        - $breakMinutes;

                                    $h = floor($workMinutes / 60);
                                    $m = $workMinutes % 60;
                                @endphp
                                {{ sprintf('%d:%02d', $h, $m) }}
                            @endif
                        </td>
                        <td>
                            @if ($attendance)
                                <a href="{{ route('admin.attendance.detail', ['user' => $attendance->user_id, 'date' => $attendance->date]) }}">
                                    詳細
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection