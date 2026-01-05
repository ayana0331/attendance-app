@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/attendance_correction_list.css') }}">
@endsection

@section('content')
<div class="page-wrapper">

    <h2 class="page-title">申請一覧</h2>

    <div class="tab-nav">
        <a href="{{ route('admin.attendance_correction.list', ['tab' => 'pending']) }}" class="tab {{ $tab === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.attendance_correction.list', ['tab' => 'approved']) }}" class="tab {{ $tab === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

    <div class="list-box">

        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>
                        {{ $item->status === 'pending' ? '承認待ち' : '承認済み' }}
                    </td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->date)->format('Y/m/d') }}</td>
                    <td class="reason">{{ $item->reason }}</td>
                    <td>{{ $item->created_at->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.attendance.detail', ['user' => $item->user->id, 'date' => $item->date,]) }}">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
