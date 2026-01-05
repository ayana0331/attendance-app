<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyApp')</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/app.css') }}">
    @yield('head')
    @yield('styles')
</head>
<body>
<header class="header">
    <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">

    <nav class="header-nav">
        <a href="{{ url('/attendance') }}">勤怠</a>
        <a href="{{ url('/attendance/list') }}">勤怠一覧</a>
        <a href="{{ url('/stamp_correction_request/list') }}">申請</a>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="btn-link">ログアウト</button>
        </form>
    </nav>
</header>

@yield('content')
</body>
</html>
