@extends('layouts.navigation')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="page-wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    @if ($correction)
    <div class="detail-box">
        <div class="detail-row">
            <div class="detail-label">名前</div>
            <div class="detail-value">
                <span class="name">
                    {{ auth()->user()->name }}
                </span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">日付</div>
            <div class="detail-value">
                <span class="year">{{ \Carbon\Carbon::parse($date)->format('Y年') }}</span>
                <span class="md">{{ \Carbon\Carbon::parse($date)->format('n月j日') }}</span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">出勤・退勤</div>
            <div class="input-area value-inline">
                <span class="time-text">{{ \Carbon\Carbon::parse($correction->clock_in)->format('H:i') }}</span>
                <span class="time-separator">〜</span>
                <span class="time-text">{{ \Carbon\Carbon::parse($correction->clock_out)->format('H:i') }}</span>
            </div>
        </div>

        @if ($correction->break_start && $correction->break_end)
        <div class="detail-row">
            <div class="detail-label">休憩</div>
            <div class="input-area value-inline">
                <span class="time-text">{{ $correction->break_start ? \Carbon\Carbon::parse($correction->break_start)->format('H:i') : '' }}</span>
                @if($correction->break_start && $correction->break_end)<span class="time-separator">〜</span>@endif
                <span class="time-text">{{ $correction->break_end ? \Carbon\Carbon::parse($correction->break_end)->format('H:i') : '' }}</span>
            </div>
        </div>
        @endif

        <div class="detail-row">
            <div class="detail-label">休憩2</div>
            <div class="input-area value-inline">
                <span class="time-text">{{ $correction->break2_start ? \Carbon\Carbon::parse($correction->break2_start)->format('H:i') : '' }}</span>
                @if($correction->break2_start && $correction->break2_end)<span class="time-separator">〜</span>@endif
                <span class="time-text">{{ $correction->break2_end ? \Carbon\Carbon::parse($correction->break2_end)->format('H:i') : '' }}</span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">備考</div>
            <div class="detail-value">
                <span class="remarks">
                    {{ $correction->reason }}
                </span>
            </div>
        </div>
    </div>
    <div class="bottom-right">
        <p class="pending-text">*承認待ちのため修正はできません。</p>
    </div>

    @else

    <form action="{{ route('attendance_correction.store') }}" method="POST">
        @csrf
        <div class="detail-box">
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">
                    <span class="name">
                        {{ auth()->user()->name }}
                    </span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value">
                    <span class="year">{{ \Carbon\Carbon::parse($date)->format('Y年') }}</span>
                    <span class="md">{{ \Carbon\Carbon::parse($date)->format('n月j日') }}</span>
                </div>
            </div>

            <div class="detail-row" style="flex-wrap: wrap; align-items: flex-start;">
                <div class="detail-label">出勤・退勤</div>
                <div class="detail-value value-inline">
                    <input type="text" name="clock_in" value="{{ ($attendance && $attendance->clock_in) ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}">
                    <span class="time-separator">〜</span>
                    <input type="text" name="clock_out" value="{{ ($attendance && $attendance->clock_out) ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}">
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('clock_in')
                        <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div>
                    @enderror
                    @if(!$errors->has('clock_in'))
                        @error('clock_out')
                            <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>

            <div class="detail-row" style="flex-wrap: wrap; align-items: flex-start;">
                <div class="detail-label">休憩</div>
                <div class="detail-value value-inline">
                    <input type="text" name="break_start" value="{{ ($attendance && $attendance->rests->get(0)) ? \Carbon\Carbon::parse($attendance->rests->get(0)->break_start)->format('H:i') : '' }}">
                    <span class="time-separator">〜</span>
                    <input type="text" name="break_end" value="{{ ($attendance && $attendance->rests->get(0)) ? \Carbon\Carbon::parse($attendance->rests->get(0)->break_end)->format('H:i') : '' }}">
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('break_start')
                        <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">
                            {{ $message }}
                        </div>
                    @enderror
                    @if(!$errors->has('break_start'))
                        @error('break_end')
                            <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </div>
            </div>

            <div class="detail-row" style="flex-wrap: wrap; align-items: flex-start;">
                <div class="detail-label">休憩2</div>
                <div class="detail-value value-inline">
                    <input type="text" name="break2_start" value="{{ ($attendance && $attendance->rests->get(1)) ? \Carbon\Carbon::parse($attendance->rests->get(1)->break_start)->format('H:i') : '' }}">
                    <span class="time-separator">〜</span>
                    <input type="text" name="break2_end" value="{{ ($attendance && $attendance->rests->get(1)) ? \Carbon\Carbon::parse($attendance->rests->get(1)->break_end)->format('H:i') : '' }}">
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('break2_start')
                        <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div>
                    @enderror
                    @if(!$errors->has('break2_start'))
                        @error('break2_end')
                            <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div>
                        @enderror
                    @endif
                </div>
            </div>

            <div class="detail-row" style="flex-wrap: wrap; align-items: flex-start;">
                <div class="detail-label">備考</div>
                <div class="detail-value">
                    <textarea name="remarks" class="textarea-full" rows="4">{{ old('remarks') }}</textarea>
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('remarks')
                        <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        @if ($attendance)
            <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        @endif
        <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
        <div class="bottom-right">
            <button type="submit" class="btn btn-primary">修正</button>
        </div>
    </form>
    @endif
</div>
@endsection