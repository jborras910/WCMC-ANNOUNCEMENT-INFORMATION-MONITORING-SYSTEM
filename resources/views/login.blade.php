@extends('layouts.index')
@section('title', 'Login — WCMC AIMS')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f0f4f8;
        }

        /* ── Layout ── */
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ── Left panel ── */
        .login-left {
            flex: 1;
            background: linear-gradient(160deg, #e0effe 0%, #bfdbfe 50%, #c7d2fe 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative blobs */
        .login-left::before {
            content: '';
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.28);
            top: -120px;
            left: -100px;
        }

        .login-left::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            bottom: -80px;
            right: -80px;
        }

        .blob-mid {
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.22);
            bottom: 120px;
            left: 40px;
            pointer-events: none;
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 380px;
        }

        .login-logo {
            width: 160px;
            margin-bottom: 32px;
            filter: drop-shadow(0 4px 12px rgba(37, 99, 235, 0.15));
        }

        .login-left h1 {
            font-size: 26px;
            font-weight: 800;
            color: #1e3a5f;
            margin-bottom: 12px;
            line-height: 1.25;
        }

        .login-left p {
            font-size: 14px;
            color: #3b6ea5;
            line-height: 1.6;
            margin-bottom: 36px;
        }

        /* Feature pills */
        .feature-pills {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }

        .feature-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 10px 16px;
            backdrop-filter: blur(4px);
        }

        .feature-pill-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-pill-icon i {
            color: #fff;
            font-size: 15px;
        }

        .feature-pill-text {
            font-size: 13px;
            font-weight: 500;
            color: #1e3a5f;
        }

        /* ── Right panel ── */
        .login-right {
            width: 440px;
            flex-shrink: 0;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 48px;
            box-shadow: -8px 0 40px rgba(0, 0, 0, 0.06);
        }

        .login-right-header {
            margin-bottom: 36px;
        }

        .login-right-header .greeting {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .login-right-header h2 {
            font-size: 26px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .login-right-header p {
            font-size: 13.5px;
            color: #64748b;
        }

        /* Alert */
        .alert-login {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #991b1b;
            animation: shake 0.45s ease;
        }

        .alert-login i {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Fields */
        .field-group {
            margin-bottom: 20px;
        }

        .field-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-bottom: 7px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            pointer-events: none;
            transition: color 0.18s;
        }

        .field-input {
            width: 100%;
            padding: 12px 14px 12px 42px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
        }

        .field-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.10);
            background: #fff;
        }

        .field-input:focus+.field-icon,
        .field-wrap:focus-within .field-icon {
            color: #2563eb;
        }

        .field-input.is-invalid {
            border-color: #ef4444;
        }

        .field-input.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.10);
        }

        .pass-wrap {
            position: relative;
        }

        .pass-wrap .field-input {
            padding-right: 44px;
        }

        .pass-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            border-radius: 5px;
            transition: color 0.15s, background 0.15s;
            display: flex;
            align-items: center;
        }

        .pass-toggle:hover {
            color: #2563eb;
            background: #eff6ff;
        }

        .field-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
        }

        /* Sign in button */
        .btn-login {
            width: 100%;
            background: #2563eb;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 14.5px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            padding: 13px;
            cursor: pointer;
            letter-spacing: 0.02em;
            margin-top: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.18s, transform 0.15s, box-shadow 0.18s;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.30);
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.38);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .btn-login:disabled {
            background: #93c5fd;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        .btn-spinner {
            width: 16px;
            height: 16px;
            border: 2.5px solid rgba(255, 255, 255, 0.4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: btn-spin 0.7s linear infinite;
            flex-shrink: 0;
        }

        @keyframes btn-spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Footer */
        .login-right-footer {
            margin-top: 36px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 800px) {
            .login-left {
                display: none;
            }

            .login-right {
                width: 100%;
                padding: 40px 28px;
                box-shadow: none;
            }
        }
    </style>

    <div class="login-page">

        {{-- Left branding panel --}}
        <div class="login-left">
            <div class="blob-mid"></div>
            <div class="login-left-content">
                <img class="login-logo" src="{{ asset('assets/wcmc_logo_1.png') }}" alt="WCMC Logo">
                <h1>Announcement Information Monitoring System</h1>
                <p>Manage, review, and publish announcements for World Citi Medical Center efficiently.</p>

                <div class="feature-pills">
                    <div class="feature-pill">
                        <div class="feature-pill-icon"><i class="mdi mdi-filmstrip"></i></div>
                        <span class="feature-pill-text">Upload &amp; manage slides</span>
                    </div>
                    <div class="feature-pill">
                        <div class="feature-pill-icon" style="background:#16a34a;"><i class="mdi mdi-check-decagram"></i>
                        </div>
                        <span class="feature-pill-text">Approve or reject announcements</span>
                    </div>
                    <div class="feature-pill">
                        <div class="feature-pill-icon" style="background:#d97706;"><i class="mdi mdi-chart-bar"></i></div>
                        <span class="feature-pill-text">Track activity &amp; history</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right login form --}}
        <div class="login-right">
            <div class="login-right-header">
                <div class="greeting">Welcome back</div>
                <h2>Sign in to your account</h2>
                <p>Enter your credentials to continue</p>
            </div>

            @if (session('status') || $errors->has('username') || $errors->has('password'))
                <div class="alert-login">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <div>
                        @if (session('status'))
                            {{ session('status') }}
                        @endif
                        @error('username')
                            {{ $message }}
                        @enderror
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            @endif

            <form method="post" action="{{ route('login.post') }}">
                @csrf

                <div class="field-group">
                    <label class="field-label">Username</label>
                    <div class="field-wrap">
                        <input type="text" name="username" id="usernameField"
                            class="field-input @error('username') is-invalid @enderror" placeholder="Enter your username"
                            value="{{ old('username') }}" autocomplete="username">
                        <i class="mdi mdi-account-outline field-icon"></i>
                    </div>
                    @error('username')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Password</label>
                    <div class="field-wrap pass-wrap">
                        <input type="password" name="password" id="passwordField"
                            class="field-input @error('password') is-invalid @enderror" placeholder="••••••••"
                            autocomplete="current-password">
                        <i class="mdi mdi-lock-outline field-icon"></i>
                        <button type="button" class="pass-toggle" onclick="togglePass()" tabindex="-1">
                            <i class="mdi mdi-eye-outline" id="passIcon" style="font-size:17px;"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" id="loginBtn" class="btn-login">
                    <i class="mdi mdi-login" style="font-size:17px;"></i>
                    Sign In
                </button>
            </form>

            <div class="login-right-footer">
                &copy; {{ date('Y') }} World Citi Medical Center<br>
                All rights reserved. — Designed by <a href="https://website-portfolio-peach-five.vercel.app/"
                    target="_blank">jborras910</a>


            </div>
        </div>

    </div>

    <script>
        function togglePass() {
            var f = document.getElementById('passwordField');
            var i = document.getElementById('passIcon');
            if (f.type === 'password') {
                f.type = 'text';
                i.className = 'mdi mdi-eye-off-outline';
            } else {
                f.type = 'password';
                i.className = 'mdi mdi-eye-outline';
            }
        }

        document.querySelector('form').addEventListener('submit', function() {
            var btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<div class="btn-spinner"></div> Signing in...';
        });
    </script>

@endsection
