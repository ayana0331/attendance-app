<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/auth/auth.css') }}">
</head>

<body>
    <header class="auth-header">
        <a href="{{ url('/') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" height="40">
        </a>
    </header>
    <main class="auth-content">
        <h2>会員登録</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div>
                <label for="name">ユーザー名</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label for="email">メールアドレス</label>
                <input id="email" type="text" name="email" value="{{ old('email') }}">
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
            <div>
                <label for="password_confirmation">確認用パスワード</label>
                <input id="password_confirmation" type="password" name="password_confirmation">
            </div>
            <button type="submit">登録する</button>
        </form>
        <p class="switch-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </main>
</body>
</html>

