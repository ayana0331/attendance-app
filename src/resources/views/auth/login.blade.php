<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
</head>

<body>
    <header class="auth-header">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" height="40">
        </a>
    </header>
    <main class="auth-content">
        <h2>ログイン</h2>
        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf
            <div>
                <label for="email">メールアドレス</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="password">パスワード</label>
                <input id="password" type="password" name="password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit">ログインする</button>
        </form>
        <p class="switch-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </p>
    </main>
</body>
</html>
