@extends('layouts.navigation')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="box">
    <div class="state">
        @if (!$attendance)
            勤務外
        @elseif ($attendance->clock_out)
            退勤済
        @elseif ($onBreak)
            休憩中
        @else
            出勤中
        @endif
    </div>

    <div class="date">
        {{ \Carbon\Carbon::now()->isoFormat('YYYY年M月D日 (ddd)') }}
    </div>

    <div class="time">
        {{ now()->format('H:i') }}
    </div>

    <div class="btns">
        @if (!$attendance)
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button type="submit" class="btn-primary">出勤</button>
            </form>

        @elseif ($attendance->clock_out)
            <div>お疲れ様でした。</div>

        @elseif ($onBreak)
            <form method="POST" action="{{ route('attendance.break_out') }}">
                @csrf
                <button type="submit" class="btn-secondary">休憩戻</button>
            </form>

        @else
            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button type="submit" class="btn-primary">退勤</button>
            </form>

            <form method="POST" action="{{ route('attendance.break_in') }}">
                @csrf
                <button type="submit" class="btn-secondary">休憩入</button>
            </form>
        @endif

    </div>
</div>
@endsection