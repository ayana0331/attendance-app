@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="page-wrapper">
    <h2 class="page-title">勤怠詳細</h2>

    @if (($correction && $correction->status == 'pending') || session('just_approved'))
    @php
        $displaySource = $correction ?: $attendance;
    @endphp
    <div class="detail-box">
        <div class="detail-row">
            <div class="detail-label">名前</div>
            <div class="detail-value">
                <span class="name">
                    {{ $user->name }}
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
                <span class="time-text">
                    {{ $displaySource && $displaySource->clock_in ? \Carbon\Carbon::parse($displaySource->clock_in)->format('H:i') : '' }}
                </span>
                <span class="time-separator">〜</span>
                <span class="time-text">
                    {{ $displaySource && $displaySource->clock_out ? \Carbon\Carbon::parse($displaySource->clock_out)->format('H:i') : '' }}
                </span>
            </div>
        </div>

        @php
            $b1Start = $correction ? $correction->break_start : ($attendance ? optional($attendance->rests->get(0))->break_start : null);
            $b1End   = $correction ? $correction->break_end   : ($attendance ? optional($attendance->rests->get(0))->break_end   : null);
        @endphp
        <div class="detail-row">
            <div class="detail-label">休憩</div>
            <div class="input-area value-inline">
                <span class="time-text">{{ $b1Start ? \Carbon\Carbon::parse($b1Start)->format('H:i') : '' }}</span>
                @if($b1Start && $b1End)
                    <span class="time-separator">〜</span>
                @endif
                <span class="time-text">{{ $b1End ? \Carbon\Carbon::parse($b1End)->format('H:i') : '' }}</span>
            </div>
        </div>

        @php
            $b2Start = $correction ? $correction->break2_start : ($attendance ? optional($attendance->rests->get(1))->break_start : null);
            $b2End   = $correction ? $correction->break2_end   : ($attendance ? optional($attendance->rests->get(1))->break_end   : null);
        @endphp
        <div class="detail-row">
            <div class="detail-label">休憩2</div>
            <div class="input-area value-inline">
                <span class="time-text">{{ $b2Start ? \Carbon\Carbon::parse($b2Start)->format('H:i') : '' }}</span>
                @if($b2Start && $b2End)
                    <span class="time-separator">〜</span>
                @endif
                <span class="time-text">{{ $b2End ? \Carbon\Carbon::parse($b2End)->format('H:i') : '' }}</span>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">備考</div>
            <div class="detail-value">
                <span class="remarks">
                    @if($correction)
                        {{ $correction->reason }}
                    @elseif(session('prev_reason'))
                        {{ session('prev_reason') }}
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="bottom-right">
        @if (session('just_approved'))
            <button class="btn btn-secondar" disabled>
                承認済み
            </button>
        @else
            <form method="POST" action="{{ route('admin.attendance_correction.approve', $correction->id) }}">
                @csrf
                <button class="btn btn-primary">承認</button>
            </form>
        @endif
    </div>

    @else
    <form action="{{ route('admin.attendance.update') }}" method="POST">
        @csrf
        <div class="detail-box">
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">
                    <span class="name">
                        {{ $user->name }}
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
                    <input type="text" name="clock_in" value="{{ $attendance ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}">
                    <span class="time-separator">〜</span>
                    <input type="text" name="clock_out" value="{{ $attendance ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}">
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('clock_in') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
                    @if(!$errors->has('clock_in'))
                        @error('clock_out') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
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
                    @error('break_start') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
                    @if(!$errors->has('break_start'))
                        @error('break_end') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
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
                    @error('break2_start') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
                    @if(!$errors->has('break2_start'))
                        @error('break2_end') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
                    @endif
                </div>
            </div>

            <div class="detail-row" style="flex-wrap: wrap; align-items: flex-start;">
                <div class="detail-label">備考</div>
                <div class="detail-value">
                    <textarea name="remarks" class="textarea-full" rows="4">{{ old('remarks') }}</textarea>
                </div>
                <div style="flex-basis: 100%; margin-left: 300px; height: 0; position: relative;">
                    @error('remarks') <div style="color:red; font-size: 0.8rem; position: absolute; top: 5px; white-space: nowrap;">{{ $message }}</div> @enderror
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