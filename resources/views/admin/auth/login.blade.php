@php
    $siteFavicon = !empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') || str_starts_with($settings['favicon_url'], '/') ? $settings['favicon_url'] : asset('storage/' . $settings['favicon_url'])) : '/images/favicon.png';
    $siteLogo = !empty($settings['logo_url']) ? (str_starts_with($settings['logo_url'], 'http') || str_starts_with($settings['logo_url'], '/') ? $settings['logo_url'] : asset('storage/' . $settings['logo_url'])) : '/images/logo.jpeg';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login | {{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</title>
    
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/css/style.css">
    
    <style>
        body.login-body {
            min-height: 100vh;
            background-color: #14080B;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow-x: hidden;
            font-family: var(--font-sans);
            color: #ffffff;
        }

        .login-card {
            background-color: rgba(31, 12, 18, 0.9);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(245, 214, 125, 0.3);
            border-radius: 16px;
            width: 100%;
            max-width: 580px;
            padding: 3.25rem 3.25rem 2.75rem;
            position: relative;
            z-index: 10;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.65), 0 0 50px rgba(200, 16, 30, 0.1);
        }

        @media (max-width: 640px) {
            .login-card {
                padding: 2.5rem 1.75rem;
                max-width: 100%;
            }
        }

        .login-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-brand-logo-img {
            width: 105px;
            height: 105px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.55), 0 0 25px rgba(245, 214, 125, 0.15);
            border: 1.5px solid rgba(245, 214, 125, 0.35);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-brand-logo-img:hover {
            transform: scale(1.03);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.65), 0 0 30px rgba(245, 214, 125, 0.25);
        }

        .login-title {
            font-family: var(--font-serif);
            font-size: 1.65rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: #ffffff;
            line-height: 1.25;
            margin: 1.1rem 0 0.25rem;
            text-align: center;
        }

        .login-sub {
            font-size: 0.8rem;
            color: var(--color-gold-light);
            letter-spacing: 0.25em;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 0.25rem;
        }

        .login-form-group {
            margin-bottom: 1.4rem;
        }

        .login-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-input {
            width: 100%;
            padding: 0.95rem 1.25rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 8px;
            color: #ffffff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .login-input:focus {
            border-color: var(--color-gold-bright);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 0 15px rgba(245, 214, 125, 0.25);
        }

        .login-btn {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, var(--color-crimson) 0%, var(--color-burgundy) 100%);
            border: 1px solid var(--color-crimson);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 0.85rem;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(200, 16, 30, 0.45);
            border-color: var(--color-gold-bright);
        }

        .login-alert {
            padding: 0.9rem 1.15rem;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background-color: rgba(200, 16, 30, 0.2);
            border: 1px solid rgba(200, 16, 30, 0.45);
            color: #ff9da6;
        }

        .alert-success {
            background-color: rgba(46, 125, 50, 0.2);
            border: 1px solid rgba(46, 125, 50, 0.45);
            color: #a5d6a7;
        }
    </style>
</head>
<body class="login-body">
    <div class="floating-bg-container" data-particles="10"></div>

    <div class="login-card">
        <div class="login-brand">
            <img src="{{ $siteLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}" class="login-brand-logo-img">
            <h1 class="login-title">{{ strtoupper($settings['site_name'] ?? 'LUMIQUE AESTHETIC CLINIC') }}</h1>
            <span class="login-sub">ADMIN PORTAL</span>
        </div>

        @if($errors->any())
        <div class="login-alert alert-error">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
        @endif

        @if(session('success'))
        <div class="login-alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="login-form-group">
                <label for="email" class="login-label">USERNAME</label>
                <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus class="login-input" placeholder="admin@lumiqueclinic.com">
            </div>

            <div class="login-form-group">
                <label for="password" class="login-label">PASSWORD</label>
                <input type="password" id="password" name="password" required class="login-input" placeholder="Enter your security password">
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; font-size: 0.82rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: rgba(255, 255, 255, 0.8);">
                    <input type="checkbox" name="remember" value="1" checked style="accent-color: var(--color-crimson);">
                    <span>Remember session</span>
                </label>
                <a href="{{ route('home') }}" style="color: var(--color-gold-light); text-decoration: none; font-weight: 500;">← Public Site</a>
            </div>

            <button type="submit" class="login-btn">Admin Sign In</button>
        </form>
    </div>
</body>
</html>
