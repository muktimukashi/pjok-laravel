<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk</title>
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body class="login-page">
    <div class="login-card-area">
        <form class="login-card" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="login-logo-block">
                <div class="logo-badge logo-image-wrap">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo MADEP PJOK" class="app-logo-image" />
                </div>
                <div>
                    <strong>MADEP PJOK</strong>
                    <span>Masuk ke aplikasi asesmen PJOK</span>
                </div>
            </div>
            <h2>Masuk Aplikasi</h2>
            <p>Masuk menggunakan email dan kata sandi untuk mengakses sistem.</p>
            @if ($errors->any())
                <div class="login-error">{{ $errors->first() }}</div>
            @endif
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required>
            </div>
            <div class="field">
                <label for="password">Kata Sandi</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div class="field">
                <label><input type="checkbox" name="remember"> Remember me</label>
            </div>
            <button class="btn btn-primary btn-large" type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>

