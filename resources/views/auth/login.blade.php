@extends('layouts.app')

@section('title', 'Sign In - UangKos')

@section('content')
<div class="login-screen-root">
    <!-- Floating Money Elements (Pengganti petir dengan animasi uang) -->
    <div class="money-sky" aria-hidden="true">
        <!-- SVG Definitions for Gradients -->
        <svg width="0" height="0" class="svg-defs">
            <defs>
                <linearGradient id="coinGold" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fef08a"/>
                    <stop offset="35%" stop-color="#facc15"/>
                    <stop offset="70%" stop-color="#eab308"/>
                    <stop offset="100%" stop-color="#a16207"/>
                </linearGradient>
                <linearGradient id="billRed" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#fb7185"/>
                    <stop offset="50%" stop-color="#e11d48"/>
                    <stop offset="100%" stop-color="#881337"/>
                </linearGradient>
                <linearGradient id="billBlue" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#38bdf8"/>
                    <stop offset="50%" stop-color="#0284c7"/>
                    <stop offset="100%" stop-color="#0c4a6e"/>
                </linearGradient>
                <linearGradient id="billGreen" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#34d399"/>
                    <stop offset="50%" stop-color="#059669"/>
                    <stop offset="100%" stop-color="#064e3b"/>
                </linearGradient>
            </defs>
        </svg>

        <!-- Uang Kertas 100 Ribu Melayang (Kiri Atas) -->
        <div class="float-item bill-red-wrap">
            <svg class="money-bill bill-100k" viewBox="0 0 130 72" fill="none">
                <rect width="130" height="72" rx="10" fill="url(#billRed)" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>
                <rect x="7" y="7" width="116" height="58" rx="6" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3 2" fill="none"/>
                <circle cx="65" cy="36" r="18" fill="rgba(255,255,255,0.18)"/>
                <text x="14" y="24" font-size="11" font-weight="900" fill="#ffffff" font-family="'Inter', sans-serif">100K</text>
                <text x="65" y="41" text-anchor="middle" font-size="14" font-weight="900" fill="#ffffff" font-family="'Inter', sans-serif">Rp</text>
                <text x="116" y="60" text-anchor="end" font-size="8.5" font-weight="700" fill="rgba(255,255,255,0.85)" font-family="'Inter', sans-serif">UANG KOS</text>
            </svg>
        </div>

        <!-- Uang Kertas 50 Ribu Melayang (Kanan Tengah) -->
        <div class="float-item bill-blue-wrap">
            <svg class="money-bill bill-50k" viewBox="0 0 120 66" fill="none">
                <rect width="120" height="66" rx="9" fill="url(#billBlue)" stroke="rgba(255,255,255,0.4)" stroke-width="1.5"/>
                <rect x="6" y="6" width="108" height="54" rx="5" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3 2" fill="none"/>
                <circle cx="60" cy="33" r="16" fill="rgba(255,255,255,0.18)"/>
                <text x="12" y="22" font-size="10" font-weight="900" fill="#ffffff" font-family="'Inter', sans-serif">50K</text>
                <text x="60" y="38" text-anchor="middle" font-size="13" font-weight="900" fill="#ffffff" font-family="'Inter', sans-serif">Rp</text>
                <text x="108" y="55" text-anchor="end" font-size="8" font-weight="700" fill="rgba(255,255,255,0.85)" font-family="'Inter', sans-serif">UANG KOS</text>
            </svg>
        </div>

        <!-- Koin Emas Rp 1 (Kanan Atas) -->
        <div class="float-item coin-wrap-1">
            <svg class="money-coin" viewBox="0 0 60 60" fill="none">
                <circle cx="30" cy="30" r="28" fill="url(#coinGold)" stroke="#fef08a" stroke-width="2"/>
                <circle cx="30" cy="30" r="22" stroke="rgba(161, 98, 7, 0.4)" stroke-dasharray="3 2" stroke-width="1.5" fill="none"/>
                <text x="30" y="36" text-anchor="middle" font-size="15" font-weight="900" fill="#713f12" font-family="'Inter', sans-serif">Rp</text>
            </svg>
        </div>

        <!-- Koin Emas Rp 2 (Kiri Tengah) -->
        <div class="float-item coin-wrap-2">
            <svg class="money-coin" viewBox="0 0 50 50" fill="none">
                <circle cx="25" cy="25" r="23" fill="url(#coinGold)" stroke="#fef08a" stroke-width="1.8"/>
                <circle cx="25" cy="25" r="18" stroke="rgba(161, 98, 7, 0.4)" stroke-dasharray="2 2" stroke-width="1.2" fill="none"/>
                <text x="25" y="30" text-anchor="middle" font-size="12" font-weight="900" fill="#713f12" font-family="'Inter', sans-serif">Rp</text>
            </svg>
        </div>

        <!-- Floating Particles & Sparkles Uang -->
        <span class="money-sparkle sp-1">✦</span>
        <span class="money-sparkle sp-2">Rp</span>
        <span class="money-sparkle sp-3">✦</span>
        <span class="money-sparkle sp-4">Rp</span>
        <span class="money-sparkle sp-5">✦</span>
        <span class="money-sparkle sp-6">Rp</span>
    </div>

    <!-- Main Container Layout (Phone Frame Aesthetic) -->
    <div class="login-phone-frame">
        <!-- Top Header Area (Dark Theme) -->
        <header class="login-header">
            <div class="login-brand-bar">
                <img src="{{ asset('app-logo.png') }}" alt="Logo UangKos" class="brand-avatar">
                <span class="brand-title">UangKos<span class="brand-dot">UI</span></span>
            </div>

            <h1 class="welcome-heading">Welcome back.</h1>
            <p class="welcome-subheading">Pick up where you left off — your cash flow is waiting.</p>
        </header>

        <!-- White Card Sheet (Bottom Sheet Floating Form) -->
        <section class="login-card-sheet">
            <!-- Tab Switcher: Sign In vs Sign Up -->
            <div class="login-tab-pill">
                <span class="tab-item active" aria-current="page">Sign In</span>
                <a href="{{ route('register') }}" class="tab-item">Sign Up</a>
            </div>

            <!-- Error Notifications -->
            @if (isset($errors) && $errors->any())
                <div class="login-alert-box" role="alert">
                    @foreach ($errors->all() as $error)
                        <div class="alert-msg">{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Login Form -->
            <form class="login-actual-form" method="POST" action="{{ route('login.store') }}">
                @csrf

                <!-- Email Input with Icon Prefix -->
                <div class="login-input-wrapper">
                    <span class="input-icon-left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                    </span>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Email address"
                        autocomplete="email"
                        required
                        autofocus
                    >
                </div>

                <!-- Password Input with Lock Icon Prefix & Eye Toggle Suffix -->
                <div class="login-input-wrapper">
                    <span class="input-icon-left" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </span>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        class="password-toggle input-icon-right"
                        type="button"
                        data-password-toggle
                        aria-label="Tampilkan password"
                        aria-controls="password"
                    >
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                            <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path>
                        </svg>
                    </button>
                </div>

                <!-- Remember Me & Forgot Password Row -->
                <div class="login-meta-row">
                    <label class="login-checkbox-label">
                        <input name="remember" type="checkbox" value="1" checked>
                        <span class="checkbox-visual">
                            <svg viewBox="0 0 12 10" fill="none">
                                <path d="M1.5 5.2L4.5 8.2L10.5 1.8" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="label-text">Keep me signed in</span>
                    </label>

                    <a href="{{ route('register') }}" class="forgot-pass-link">Forgot Password?</a>
                </div>

                <!-- Submit Button (Vibrant Blue) -->
                <button class="login-submit-button" type="submit">
                    <span>Sign in</span>
                </button>
            </form>

            <!-- Bottom Create Account Link (Google & Apple ditiadakan sesuai permintaan) -->
            <div class="login-bottom-callout">
                <span>New to UangKos?</span>
                <a href="{{ route('register') }}" class="callout-link">Create an account</a>
            </div>
        </section>
    </div>
</div>

<style>
    /* ==========================================================================
       DailyFlutterUI Style Login Screen with Floating Money Animation
       ========================================================================== */
    @import url('https://fonts.bunny.net/css?family=instrument-serif:400,400i|inter:400,500,600,700,800');

    /* Break out of standard layout container */
    body:has(.login-screen-root) {
        background: #0b0e14 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden;
    }
    body:has(.login-screen-root)::before,
    body:has(.login-screen-root)::after {
        display: none !important;
    }
    body:has(.login-screen-root) .page-shell.auth-shell {
        width: 100% !important;
        max-width: 100% !important;
        min-height: 100vh !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .login-screen-root {
        position: relative;
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle at 50% 20%, #162032 0%, #080b11 85%);
        padding: 24px 16px;
        box-sizing: border-box;
        overflow: hidden;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* ── Floating Money & Currency Effect (Pengganti Petir) ── */
    .money-sky {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
        z-index: 1;
    }
    .svg-defs {
        position: absolute;
        width: 0;
        height: 0;
    }

    .float-item {
        position: absolute;
        will-change: transform;
    }

    /* Uang Kertas 100K */
    .bill-red-wrap {
        top: 6%;
        left: 4%;
        width: 135px;
        filter: drop-shadow(0 14px 24px rgba(225, 29, 72, 0.35));
        animation: floatRedBill 6.5s ease-in-out infinite alternate;
        opacity: 0.88;
    }
    .bill-100k {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Uang Kertas 50K */
    .bill-blue-wrap {
        top: 22%;
        right: 4%;
        width: 125px;
        filter: drop-shadow(0 14px 24px rgba(2, 132, 199, 0.35));
        animation: floatBlueBill 7.2s ease-in-out infinite alternate;
        opacity: 0.85;
    }
    .bill-50k {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Koin Emas */
    .coin-wrap-1 {
        top: 9%;
        right: 14%;
        width: 58px;
        filter: drop-shadow(0 10px 20px rgba(234, 179, 8, 0.45));
        animation: floatGoldCoin1 5s ease-in-out infinite alternate;
    }
    .coin-wrap-2 {
        top: 29%;
        left: 6%;
        width: 48px;
        filter: drop-shadow(0 10px 18px rgba(234, 179, 8, 0.4));
        animation: floatGoldCoin2 6s ease-in-out infinite alternate;
    }
    .money-coin {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Floating Particles */
    .money-sparkle {
        position: absolute;
        font-weight: 800;
        color: #fde047;
        opacity: 0;
        text-shadow: 0 0 12px rgba(253, 224, 71, 0.8);
        animation: moneySparkleDrift infinite linear;
    }
    .sp-1 { top: 40%; left: 18%; font-size: 16px; animation-duration: 7s; }
    .sp-2 { top: 18%; left: 38%; font-size: 14px; animation-duration: 9s; animation-delay: 1.5s; color: #38bdf8; }
    .sp-3 { top: 12%; right: 35%; font-size: 18px; animation-duration: 8s; animation-delay: 2.2s; }
    .sp-4 { top: 48%; right: 12%; font-size: 13px; animation-duration: 6.5s; animation-delay: 0.8s; color: #34d399; }
    .sp-5 { top: 60%; left: 8%; font-size: 15px; animation-duration: 7.5s; animation-delay: 3s; }
    .sp-6 { top: 75%; right: 10%; font-size: 14px; animation-duration: 8.5s; animation-delay: 1.2s; color: #fde047; }

    @keyframes floatRedBill {
        0% { transform: translateY(0) rotate(-16deg) scale(1); }
        50% { transform: translateY(-18px) rotate(-10deg) scale(1.04); }
        100% { transform: translateY(0) rotate(-16deg) scale(1); }
    }
    @keyframes floatBlueBill {
        0% { transform: translateY(0) rotate(18deg) scale(1); }
        50% { transform: translateY(-22px) rotate(24deg) scale(1.05); }
        100% { transform: translateY(0) rotate(18deg) scale(1); }
    }
    @keyframes floatGoldCoin1 {
        0% { transform: translateY(0) rotate(0deg) scale(1); }
        50% { transform: translateY(-16px) rotate(16deg) scale(1.08); }
        100% { transform: translateY(0) rotate(0deg) scale(1); }
    }
    @keyframes floatGoldCoin2 {
        0% { transform: translateY(0) rotate(0deg) scale(1); }
        50% { transform: translateY(-14px) rotate(-18deg) scale(1.06); }
        100% { transform: translateY(0) rotate(0deg) scale(1); }
    }
    @keyframes moneySparkleDrift {
        0% { transform: translateY(0) scale(0.6); opacity: 0; }
        25% { opacity: 0.85; }
        75% { opacity: 0.85; }
        100% { transform: translateY(-70px) scale(1.1); opacity: 0; }
    }

    /* ── Phone Frame Container ── */
    .login-phone-frame {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 390px;
        display: flex;
        flex-direction: column;
        margin: 0 auto;
        animation: phoneEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    @keyframes phoneEntrance {
        0% { opacity: 0; transform: translateY(28px) scale(0.97); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ── Header ── */
    .login-header {
        padding: 10px 14px 26px;
        color: #ffffff;
    }
    .login-brand-bar {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 22px;
    }
    .brand-avatar {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: 0 4px 14px rgba(56, 189, 248, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .brand-title {
        font-size: 1.12rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #ffffff;
        font-style: italic;
    }
    .brand-dot {
        color: #38bdf8;
        font-style: italic;
        margin-left: 1px;
    }

    .welcome-heading {
        margin: 0 0 8px;
        font-family: 'Instrument Serif', 'Playfair Display', Georgia, serif;
        font-size: 2.35rem;
        font-weight: 400;
        font-style: italic;
        letter-spacing: -0.01em;
        line-height: 1.15;
        color: #ffffff;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
    }
    .welcome-subheading {
        margin: 0;
        font-size: 0.86rem;
        line-height: 1.45;
        color: #94a3b8;
        font-weight: 400;
    }

    /* ── White Card Sheet ── */
    .login-card-sheet {
        position: relative;
        background: #ffffff;
        border-radius: 34px;
        padding: 22px 20px 24px;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.1);
        display: flex;
        flex-direction: column;
    }

    /* Segmented Tab Pill */
    .login-tab-pill {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        background: #f1f4f9;
        border-radius: 15px;
        padding: 4px;
        margin-bottom: 20px;
    }
    .tab-item {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        user-select: none;
    }
    .tab-item.active {
        background: #111827;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(17, 24, 39, 0.2);
        cursor: default;
    }
    .tab-item:not(.active) {
        color: #64748b;
        background: transparent;
    }
    .tab-item:not(.active):hover {
        color: #0f172a;
        background: rgba(255, 255, 255, 0.6);
    }

    /* Error Alert */
    .login-alert-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 14px;
        padding: 10px 14px;
        margin-bottom: 16px;
        color: #b91c1c;
        font-size: 0.82rem;
        font-weight: 500;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    /* Actual Form */
    .login-actual-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* Soft Rounded Input Groups */
    .login-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #f3f5f9;
        border: 1.5px solid #e7ecf3;
        border-radius: 16px;
        transition: all 0.2s ease;
    }
    .login-input-wrapper:focus-within {
        background: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
    }

    .input-icon-left {
        display: flex;
        align-items: center;
        justify-content: center;
        padding-left: 14px;
        color: #94a3b8;
        pointer-events: none;
    }
    .input-icon-left svg {
        width: 19px;
        height: 19px;
    }

    .login-input-wrapper input {
        width: 100%;
        min-height: 48px;
        padding: 12px 14px 12px 10px;
        background: transparent;
        border: none;
        outline: none;
        font-size: 0.92rem;
        font-weight: 500;
        color: #1e293b;
        border-radius: 16px;
        font-family: inherit;
        box-shadow: none !important;
    }
    .login-input-wrapper input::placeholder {
        color: #94a3b8;
        font-weight: 400;
    }

    /* Password Toggle Icon Button */
    .input-icon-right {
        background: transparent;
        border: none;
        padding: 0 14px 0 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        cursor: pointer;
        outline: none;
        box-shadow: none !important;
        transform: none !important;
    }
    .input-icon-right:hover {
        color: #475569;
        transform: none !important;
        box-shadow: none !important;
    }
    .input-icon-right svg {
        width: 19px;
        height: 19px;
    }

    /* Options Row: Remember & Forgot */
    .login-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 2px;
        margin-bottom: 6px;
        font-size: 0.82rem;
    }
    .login-checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
        color: #475569;
        font-weight: 500;
    }
    .login-checkbox-label input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
    }
    .checkbox-visual {
        width: 18px;
        height: 18px;
        border-radius: 6px;
        border: 1.5px solid #cbd5e1;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        flex-shrink: 0;
    }
    .checkbox-visual svg {
        width: 11px;
        height: 11px;
        opacity: 0;
        transform: scale(0.6);
        transition: all 0.15s ease;
    }
    .login-checkbox-label input[type="checkbox"]:checked + .checkbox-visual {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }
    .login-checkbox-label input[type="checkbox"]:checked + .checkbox-visual svg {
        opacity: 1;
        transform: scale(1);
    }

    .forgot-pass-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.15s ease;
    }
    .forgot-pass-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Primary Submit Button (Vibrant Blue like screenshot) */
    .login-submit-button {
        width: 100%;
        min-height: 50px;
        border-radius: 16px;
        border: none;
        background: #3b82f6;
        color: #ffffff;
        font-size: 0.96rem;
        font-weight: 700;
        letter-spacing: -0.01em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 22px rgba(59, 130, 246, 0.35);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        margin-top: 4px;
    }
    .login-submit-button:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(59, 130, 246, 0.45);
    }
    .login-submit-button:active {
        transform: translateY(0) scale(0.98);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    }

    /* Bottom Create Account Callout */
    .login-bottom-callout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 22px;
        font-size: 0.83rem;
        color: #64748b;
        font-weight: 500;
    }
    .callout-link {
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.15s;
    }
    .callout-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    /* Media query adjustments */
    @media (max-width: 480px) {
        .login-screen-root {
            padding: 12px 10px;
            align-items: flex-end;
        }
        .login-phone-frame {
            max-width: 100%;
        }
        .welcome-heading {
            font-size: 2.1rem;
        }
        .bill-red-wrap {
            width: 105px;
            top: 2%;
            left: 2%;
        }
        .bill-blue-wrap {
            width: 95px;
            top: 14%;
            right: 2%;
        }
        .coin-wrap-1 {
            width: 44px;
            top: 4%;
            right: 12%;
        }
        .coin-wrap-2 {
            width: 38px;
            top: 22%;
            left: 4%;
        }
    }
</style>
@endsection
