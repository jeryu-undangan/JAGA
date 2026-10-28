<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Pengatur Uang Kos')</title>
    <link rel="icon" type="image/png" href="{{ asset('app-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('app-logo.png') }}">
    <style>
        :root { font-family: Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1d2939; background: #edf3ff; font-synthesis: none; }
        * { box-sizing: border-box; }
        body { position: relative; isolation: isolate; margin: 0; min-height: 100vh; background: linear-gradient(135deg, #eef5ff 0%, #f8f6ff 46%, #eaf8f5 100%); background-attachment: fixed; }
        body::before, body::after { content: ''; position: fixed; z-index: -1; width: 360px; height: 360px; border-radius: 50%; filter: blur(3px); pointer-events: none; }
        body::before { top: 8%; left: -150px; background: radial-gradient(circle, rgba(91, 156, 255, .2), rgba(91, 156, 255, 0) 70%); }
        body::after { right: -130px; bottom: 2%; background: radial-gradient(circle, rgba(139, 111, 255, .16), rgba(139, 111, 255, 0) 70%); }
        button, input, select { font: inherit; }
        button, .button { border: 0; border-radius: 11px; padding: 11px 15px; font-weight: 700; text-align: center; text-decoration: none; cursor: pointer; box-shadow: 0 5px 12px rgba(31, 54, 87, .08); transition: background .2s ease, transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        button:hover, .button:hover { transform: translateY(-2px); box-shadow: 0 9px 18px rgba(31, 54, 87, .15); }
        button:active, .button:active { transform: translateY(0) scale(.97); box-shadow: 0 3px 8px rgba(31, 54, 87, .12); }
        button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible { outline: 3px solid rgba(8, 121, 249, .3); outline-offset: 3px; }
        .topbar { position: sticky; top: 0; z-index: 20; height: 76px; display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 0 max(22px, calc((100vw - 1160px) / 2)); background: linear-gradient(105deg, rgba(255,255,255,.94), rgba(239,246,255,.92) 52%, rgba(246,240,255,.92)); border-bottom: 1px solid rgba(218, 228, 243, .8); box-shadow: 0 8px 24px rgba(34, 66, 112, .09); backdrop-filter: blur(16px); }
        .topbar-leading { display: flex; align-items: center; gap: 16px; }
        .menu-wrap { position: relative; }
        .menu-button { width: 40px; height: 40px; display: grid; place-content: center; gap: 5px; padding: 8px; background: linear-gradient(145deg, #f4f8ff, #e8f0ff); }
        .menu-button span { width: 22px; height: 2px; border-radius: 2px; background: #23344d; }
        .menu-panel { display: none; position: absolute; z-index: 25; top: 49px; left: 0; width: 240px; padding: 9px; border: 1px solid #e5ebf3; border-radius: 15px; background: linear-gradient(145deg, #fff 0%, #edf5ff 48%, #f0eaff 100%); box-shadow: 0 14px 34px rgba(63,76,145,.2); }
        .menu-panel.open { display: flex; flex-direction: column; }
        .menu-panel a { padding: 11px 12px; border-radius: 8px; color: #34445b; text-decoration: none; font-size: .9rem; }
        .menu-panel a:hover, .menu-panel a.active { background: #f2f7ff; color: #0879f9; }
        .menu-panel .menu-logout { width: 100%; margin-top: 9px; padding: 12px; border-top: 1px solid #e1e8f2; }
        .brand { display: flex; align-items: center; gap: 12px; color: #172b4d; text-decoration: none; font-weight: 800; font-size: 1.05rem; }
        .brand-icon { width: 42px; height: 42px; display: block; flex: 0 0 auto; border-radius: 12px; object-fit: cover; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .profile-wrap { position: relative; }
        .profile-toggle { width: 42px; height: 42px; display: grid; place-items: center; padding: 0; border: 2px solid #d7e9ff; border-radius: 50%; background: linear-gradient(145deg, #f1f7ff, #d8eaff); color: #0866cf; font-weight: 800; box-shadow: 0 5px 15px rgba(8, 121, 249, .18); }
        .profile-panel { display: none; position: absolute; z-index: 12; top: 52px; right: 0; width: min(290px, calc(100vw - 28px)); padding: 16px; border: 1px solid #e5ebf3; border-radius: 14px; background: linear-gradient(145deg, #fff 0%, #eaf4ff 48%, #f0eaff 100%); box-shadow: 0 14px 34px rgba(63,76,145,.2); }
        .profile-panel.open { display: block; }
        .profile-email { overflow-wrap: anywhere; margin: 0 0 14px; color: #68778d; font-size: .85rem; }
        .profile-links { display: grid; gap: 6px; }
        .profile-links a { padding: 10px 11px; border-radius: 8px; color: #34445b; text-decoration: none; font-size: .9rem; }
        .profile-links a:hover { background: #f2f7ff; color: #0879f9; }
        .profile-panel form { margin-top: 8px; }
        .profile-logout { width: 100%; color: #c54040; background: #fff0ef; text-align: left; }
        .theme-fab { position: fixed; z-index: 30; right: 26px; bottom: 24px; }
        .theme-fab button { display: grid; width: 46px; height: 46px; place-items: center; padding: 0; border: 1px solid rgba(255,255,255,.48); border-radius: 50%; color: #fff; background: linear-gradient(130deg, #1686ff, #6850db 58%, #a047bb); box-shadow: 0 8px 20px rgba(68,75,184,.28), inset 0 1px 0 rgba(255,255,255,.38); }
        .theme-fab button:hover { box-shadow: 0 16px 34px rgba(68,75,184,.4), inset 0 1px 0 rgba(255,255,255,.42); }
        .theme-fab-icon { display: block; width: 20px; height: 20px; }
        .bottom-nav { position: fixed; z-index: 24; right: 0; bottom: max(14px, env(safe-area-inset-bottom)); left: 0; display: grid; grid-template-columns: minmax(0, 1fr) 0 minmax(0, 1fr); width: min(680px, calc(100% - 32px)); min-height: 82px; margin: 0 auto; padding: 10px 14px; overflow: hidden; border: 1px solid rgba(214,224,240,.92); border-radius: 32px; background: rgba(255,255,255,.94); box-shadow: 0 12px 34px rgba(43,65,112,.2), inset 0 2px 0 rgba(255,255,255,.95); backdrop-filter: blur(18px); }
        .bottom-nav-group { display: flex; min-width: 0; align-items: stretch; }
        .bottom-nav-group a { flex: 1 1 0; }
        .bottom-nav-group:last-child a { flex: 1 1 0; }
        .bottom-nav-home { z-index: 1; justify-self: center; width: 76px; }
        .bottom-nav a { position: relative; isolation: isolate; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; min-width: 0; padding: 5px 3px; border-radius: 22px; color: #78849a; text-decoration: none; font-size: .68rem; font-weight: 600; -webkit-tap-highlight-color: transparent; touch-action: manipulation; transition: color .2s ease, background .2s ease, transform .2s cubic-bezier(.2,.8,.2,1); }
        .bottom-nav a:hover { color: #0879f9; background: #f3f7ff; }
        .bottom-nav a:active { transform: scale(.88); }
        .bottom-nav a.active { color: #0879f9; animation: nav-bubble-in .42s cubic-bezier(.2,.8,.2,1) both; }
        .bottom-nav a.active::before { position: absolute; top: 0; z-index: -1; width: 42px; height: 42px; border: 1px solid rgba(255,255,255,.9); border-radius: 50%; background: radial-gradient(circle at 35% 25%, #fff 0, #edf5ff 46%, #dceaff 100%); box-shadow: 0 4px 12px rgba(50,112,203,.16), inset 0 1px 2px #fff; content: ''; }
        .bottom-nav svg { width: 21px; height: 21px; stroke: currentColor; transition: transform .2s cubic-bezier(.2,.8,.2,1); }
        .bottom-nav a:active svg { transform: scale(.82); }
        .bottom-nav a.active::after { position: absolute; bottom: -3px; width: 18px; height: 3px; border-radius: 4px; background: #0879f9; content: ''; }
        .bottom-nav-ripple { position: absolute; z-index: -1; width: 12px; height: 12px; border-radius: 50%; background: rgba(8,121,249,.2); pointer-events: none; transform: translate(-50%, -50%) scale(0); animation: nav-ripple .52s ease-out forwards; }
        @keyframes nav-ripple { to { opacity: 0; transform: translate(-50%, -50%) scale(8); } }
        @keyframes nav-bubble-in { 0% { transform: translateY(5px) scale(.88); } 65% { transform: translateY(-2px) scale(1.04); } 100% { transform: translateY(0) scale(1); } }
        body.salary-mode { color: #1F2937; background: #F4F7F5; }
        body.salary-mode::before { background: radial-gradient(circle, rgba(16,185,129,.11), rgba(16,185,129,0) 70%); }
        body.salary-mode::after { background: radial-gradient(circle, rgba(6,95,70,.09), rgba(6,95,70,0) 70%); }
        body.salary-mode .topbar { background: rgba(255,255,255,.92); border-color: #e2ebe5; }
        body.salary-mode .brand { color: #065f46; }
        body.salary-mode .menu-button { background: linear-gradient(145deg, #fff, #eaf5ef); }
        body.salary-mode .menu-button span { background: #065f46; }
        body.salary-mode .menu-panel { border-color: #dce9e1; background: linear-gradient(145deg, #fff, #f0f8f3); }
        body.salary-mode .menu-panel a:hover, body.salary-mode .menu-panel a.active { color: #047857; background: #e8f5ee; }
        body.salary-mode .card { border-color: #e7ece8; background: #fff; box-shadow: 0 7px 22px rgba(31,41,55,.055), 0 1px 3px rgba(31,41,55,.035); }
        body.salary-mode .card:hover { border-color: #cfe3d7; box-shadow: 0 12px 28px rgba(6,95,70,.09); }
        body.salary-mode .page-heading h1, body.salary-mode .card h2, body.salary-mode .card h3 { color: #1f2937; }
        body.salary-mode .page-heading p, body.salary-mode .section-heading p, body.salary-mode .salary-summary-card > span, body.salary-mode .salary-hint { color: #4b5563; }
        body.salary-mode .salary-summary-card { position: relative; padding-left: 27px; }
        body.salary-mode .salary-summary-card::before { position: absolute; top: 26px; left: 19px; width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.12); content: ''; }
        body.salary-mode .salary-income-card::before { background: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.12); }
        body.salary-mode .salary-expense-card::before { background: #EF4444; box-shadow: 0 0 0 4px rgba(239,68,68,.11); }
        body.salary-mode .salary-summary-card strong { color: #059669; }
        body.salary-mode .salary-income-card strong { color: #059669; }
        body.salary-mode .salary-expense-card strong { color: #EF4444; }
        body.salary-mode .salary-summary-primary { border-color: #dceee4; background: #fff; }
        body.salary-mode .salary-balance-card { grid-column: 1 / -1; min-height: 158px; padding: 23px 25px; overflow: hidden; color: #fff; border: 1px solid rgba(255,255,255,.28); background: radial-gradient(ellipse at 100% 0%, rgba(255,255,255,.2), transparent 46%), linear-gradient(125deg, #087f5b 0%, #065f46 52%, #064e3b 100%); box-shadow: 0 14px 30px rgba(6,95,70,.22), inset 0 1px 0 rgba(255,255,255,.2); }
        body.salary-mode .salary-balance-card::after { position: absolute; top: -86px; right: -52px; width: 210px; height: 210px; border: 1px solid rgba(255,255,255,.15); border-radius: 50%; box-shadow: 0 0 0 25px rgba(255,255,255,.045), 0 0 0 55px rgba(255,255,255,.025); content: ''; pointer-events: none; }
        body.salary-mode .salary-balance-card > span { position: relative; z-index: 1; color: rgba(255,255,255,.84); }
        body.salary-mode .salary-balance-card strong { position: relative; z-index: 1; display: block; margin-top: 9px; color: #fff; font-size: clamp(1.7rem, 3vw, 2.25rem); letter-spacing: -.04em; }
        body.salary-mode .salary-balance-card .salary-balance-note { position: relative; z-index: 1; margin-top: 17px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,.24); color: rgba(255,255,255,.82); font-size: .78rem; }
        body.dark-mode.salary-mode .salary-balance-card { background: radial-gradient(ellipse at 100% 0%, rgba(255,255,255,.12), transparent 46%), linear-gradient(125deg, #17634b, #124c3c 52%, #103e33); }
        body.salary-mode .btn-main { color: #fff; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 15px rgba(5,150,105,.2); }
        body.salary-mode .btn-main:hover { background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 9px 20px rgba(5,150,105,.25); }
        body.salary-mode .btn-quiet { color: #065f46; border-color: #065f46; background: #fff; box-shadow: 0 4px 10px rgba(6,95,70,.07); }
        body.salary-mode .btn-quiet:hover { color: #047857; background: #f0f8f3; }
        body.salary-mode .btn-green { background: linear-gradient(135deg, #10b981, #059669); }
        body.salary-mode .pemasukan-text { color: #059669; }
        body.salary-mode .pengeluaran-text { color: #ef4444; }
        body.salary-mode .salary-chart-track > span { background: linear-gradient(90deg, #10b981, #059669); }
        body.salary-mode .salary-budget-track > span { background: linear-gradient(90deg, #10b981, #059669); }
        body.salary-mode .salary-budget-track.warning > span { background: linear-gradient(90deg, #f2b63f, #ed8b32); }
        body.salary-mode .salary-budget-track.danger > span { background: linear-gradient(90deg, #f87171, #EF4444); }
        body.salary-mode .bottom-nav { border-color: #e1eae4; background: rgba(255,255,255,.96); box-shadow: 0 12px 34px rgba(6,95,70,.12), inset 0 2px 0 rgba(255,255,255,.98); }
        body.salary-mode .bottom-nav a:hover { color: #047857; background: #eff8f3; }
        body.salary-mode .bottom-nav a.active { color: #059669; }
        body.salary-mode .bottom-nav a.active::before { background: radial-gradient(circle at 35% 25%, #fff 0, #e9f8f0 48%, #d7f2e4 100%); box-shadow: 0 4px 12px rgba(5,150,105,.16), inset 0 1px 2px #fff; }
        body.salary-mode .bottom-nav a.active::after { background: #059669; }
        body.salary-mode .bottom-nav-ripple { background: rgba(16,185,129,.22); }
        body.salary-mode .theme-fab button { background: linear-gradient(135deg, #065f46, #047857); box-shadow: 0 8px 20px rgba(6,95,70,.25), inset 0 1px 0 rgba(255,255,255,.3); }
        body.salary-mode .theme-fab button:hover { box-shadow: 0 14px 28px rgba(6,95,70,.32), inset 0 1px 0 rgba(255,255,255,.35); }
        body.salary-mode input, body.salary-mode select { color: #1f2937; border-color: #dce7e0; background: #fff; box-shadow: inset 0 1px 2px rgba(31,41,55,.025), 0 3px 8px rgba(6,95,70,.035); }
        body.salary-mode input:focus, body.salary-mode select:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.13); }
        body.kos-mode { color: #1f2937; background: #eaf1fb; }
        body.kos-mode::before { background: radial-gradient(circle, rgba(150,188,235,.22), rgba(150,188,235,0) 70%); }
        body.kos-mode::after { background: radial-gradient(circle, rgba(123,163,214,.16), rgba(123,163,214,0) 70%); }
        body.kos-mode .topbar { background: rgba(255,255,255,.96); border-color: #dce5f1; }
        body.kos-mode .brand, body.kos-mode .menu-button span { color: #17365d; }
        body.kos-mode .menu-button { background: #f1f5fa; }
        body.kos-mode .menu-button span { background: #17365d; }
        body.kos-mode .menu-panel { border-color: #dce5f1; background: #fff; }
        body.kos-mode .menu-panel a:hover, body.kos-mode .menu-panel a.active { color: #17365d; background: #edf3fa; }
        body.kos-mode .page-shell { background: #f7f8fa; box-shadow: 0 0 32px rgba(42,73,116,.055); }
        body.kos-mode .card { border-color: #e4e8ee; background: #fff; box-shadow: 0 7px 22px rgba(31,41,55,.055), 0 1px 3px rgba(31,41,55,.035); }
        body.kos-mode .card:hover { border-color: #d1dbe8; box-shadow: 0 12px 28px rgba(23,54,93,.09); }
        body.kos-mode .page-heading h1, body.kos-mode .card h2, body.kos-mode .card h3 { color: #1f2937; }
        body.kos-mode .page-heading p, body.kos-mode .section-heading p, body.kos-mode .metric-top, body.kos-mode .schedule-description, body.kos-mode .schedule-hint { color: #667085; }
        body.kos-mode .btn-main, body.kos-mode .btn-green { color: #fff; background: linear-gradient(135deg, #214b7b, #17365d); box-shadow: 0 6px 15px rgba(23,54,93,.18); }
        body.kos-mode .btn-main:hover, body.kos-mode .btn-green:hover { background: linear-gradient(135deg, #17365d, #102a4a); box-shadow: 0 9px 20px rgba(23,54,93,.24); }
        body.kos-mode .btn-quiet { color: #17365d; border-color: #17365d; background: #fff; box-shadow: 0 4px 10px rgba(23,54,93,.07); }
        body.kos-mode .btn-quiet:hover { color: #102a4a; background: #f0f4f9; }
        body.kos-mode .metric-icon { color: #17365d; background: #edf3fa; }
        body.kos-mode .bottom-nav { border-color: #dce5f1; background: rgba(255,255,255,.97); box-shadow: 0 12px 34px rgba(23,54,93,.14), inset 0 2px 0 #fff; }
        body.kos-mode .bottom-nav a:hover { color: #17365d; background: #f0f4f9; }
        body.kos-mode .bottom-nav a.active { color: #17365d; }
        body.kos-mode .bottom-nav a.active::before { background: radial-gradient(circle at 35% 25%, #fff 0, #eef3f9 48%, #dbe6f2 100%); box-shadow: 0 4px 12px rgba(23,54,93,.16), inset 0 1px 2px #fff; }
        body.kos-mode .bottom-nav a.active::after { background: #17365d; }
        body.kos-mode .bottom-nav-ripple { background: rgba(23,54,93,.2); }
        body.kos-mode .theme-fab button { background: linear-gradient(135deg, #214b7b, #17365d); box-shadow: 0 8px 20px rgba(23,54,93,.24), inset 0 1px 0 rgba(255,255,255,.3); }
        body.kos-mode .theme-fab button:hover { box-shadow: 0 14px 28px rgba(23,54,93,.3), inset 0 1px 0 rgba(255,255,255,.35); }
        body.kos-mode input, body.kos-mode select { color: #1f2937; border-color: #d8e0ea; background: #fff; box-shadow: inset 0 1px 2px rgba(31,41,55,.025), 0 3px 8px rgba(23,54,93,.035); }
        body.kos-mode input:focus, body.kos-mode select:focus { border-color: #486b94; box-shadow: 0 0 0 4px rgba(23,54,93,.12); }
        body.kos-mode .daily-budget-card { position: relative; overflow: hidden; border-color: #cad9eb; background: radial-gradient(ellipse at 100% 0%, rgba(177,205,237,.44), transparent 48%), linear-gradient(135deg, #fff 0%, #f2f6fb 54%, #e8eff8 100%); }
        body.kos-mode .daily-budget-card::after { position: absolute; top: -52px; right: -42px; width: 150px; height: 150px; border: 1px solid rgba(23,54,93,.08); border-radius: 50%; box-shadow: 0 0 0 18px rgba(23,54,93,.025), 0 0 0 38px rgba(23,54,93,.02); content: ''; pointer-events: none; }
        body.kos-mode .daily-budget-warning { border-color: #ead7a9; background: radial-gradient(ellipse at 100% 0%, rgba(244,211,138,.3), transparent 48%), linear-gradient(135deg, #fffdf7, #f8f2e5 58%, #f2ead9); }
        body.kos-mode .daily-budget-danger { border-color: #ebc5c8; background: radial-gradient(ellipse at 100% 0%, rgba(242,178,182,.3), transparent 48%), linear-gradient(135deg, #fffafa, #f9eeee 58%, #f3e7eb); }
        body.kos-mode .daily-budget-eyebrow { color: #486b94; }
        body.kos-mode .daily-budget-heading h2 { color: #17365d; }
        body.kos-mode .daily-budget-stat { position: relative; overflow: hidden; border-color: #dce5f1; background: linear-gradient(145deg, rgba(255,255,255,.92), rgba(237,243,250,.88)); box-shadow: 0 4px 12px rgba(23,54,93,.045); }
        body.kos-mode .daily-budget-stat::before { position: absolute; top: 12px; bottom: 12px; left: 0; width: 3px; border-radius: 0 4px 4px 0; background: linear-gradient(180deg, #6f91b7, #17365d); content: ''; }
        body.kos-mode .daily-budget-stat:nth-child(2) { background: linear-gradient(145deg, #fff, #eaf1f9); }
        body.kos-mode .daily-budget-stat:nth-child(3) { border-color: #ecd9d7; background: linear-gradient(145deg, #fff, #f9efed); }
        body.kos-mode .daily-budget-stat:nth-child(3)::before { background: linear-gradient(180deg, #e1a29a, #b95350); }
        body.kos-mode .daily-budget-stat strong { color: #17365d; }
        body.kos-mode .daily-budget-stat:nth-child(3) strong { color: #a94442; }
        body.kos-mode .card-saldo { color: #fff; border: 1px solid rgba(255,255,255,.28); background: radial-gradient(ellipse at 100% 0%, rgba(255,255,255,.22), transparent 46%), linear-gradient(125deg, #315d8d 0%, #214b7b 48%, #17365d 100%); box-shadow: 0 14px 30px rgba(23,54,93,.24), inset 0 1px 0 rgba(255,255,255,.2); }
        body.kos-mode .card-saldo h2 { color: #fff; }
        body.kos-mode .card-saldo .balance-label { color: rgba(255,255,255,.82); }
        body.kos-mode .card-saldo .balance-meta { border-color: rgba(255,255,255,.24); color: rgba(255,255,255,.86); }
        body.kos-mode .card-saldo .balance-meta strong { color: #fff; }
        body.kos-mode .card-saldo::after { border-color: rgba(255,255,255,.15); box-shadow: 0 0 0 25px rgba(255,255,255,.045), 0 0 0 55px rgba(255,255,255,.025); }
        body.mode-selection { color: #1f2937; background: #eaf1fb; }
        body.mode-selection::before { background: radial-gradient(circle, rgba(150,188,235,.22), rgba(150,188,235,0) 70%); }
        body.mode-selection::after { background: radial-gradient(circle, rgba(123,163,214,.16), rgba(123,163,214,0) 70%); }
        body.mode-selection .mode-picker-brand { color: #17365d; }
        body.mode-selection .mode-picker-card { border-color: #e4e8ee; background: #fff; }
        body.mode-selection .mode-picker-eyebrow { color: #486b94; }
        body.mode-selection .mode-picker-card h1, body.mode-selection .mode-choice-card strong { color: #1f2937; }
        body.mode-selection .mode-picker-card > p, body.mode-selection .mode-choice-card small { color: #667085; }
        .mode-options { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .mode-choice { position: relative; display: block; margin: 0; cursor: pointer; }
        .mode-choice > input { position: absolute; width: 1px; height: 1px; opacity: 0; }
        .mode-choice-card { position: relative; display: flex; min-height: 205px; flex-direction: column; align-items: flex-start; gap: 10px; padding: 20px; border: 1px solid #d9e4f4; border-radius: 17px; background: linear-gradient(145deg, #fff, #f6f8fb); transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease, background .18s ease; }
        .mode-choice:hover .mode-choice-card { transform: translateY(-3px); border-color: #9bb2ce; box-shadow: 0 10px 24px rgba(23,54,93,.1); }
        .mode-choice > input:focus-visible + .mode-choice-card { outline: 3px solid rgba(23,54,93,.24); outline-offset: 3px; }
        .mode-choice > input:checked + .mode-choice-card { border-color: #17365d; background: #f1f5fa; box-shadow: 0 0 0 3px rgba(23,54,93,.1), 0 10px 24px rgba(23,54,93,.08); }
        .mode-choice-icon { display: grid; width: 46px; height: 46px; place-items: center; border-radius: 14px; color: #17365d; background: #eaf0f7; }
        .mode-choice-icon svg { width: 25px; height: 25px; }
        .mode-choice-card strong { font-size: 1.02rem; }
        .mode-choice-card small { color: #718096; font-size: .82rem; font-weight: 400; line-height: 1.5; }
        .mode-choice-check { position: absolute; top: 16px; right: 16px; width: 21px; height: 21px; border: 1.5px solid #bdc9d8; border-radius: 50%; background: #fff; }
        .mode-choice > input:checked + .mode-choice-card .mode-choice-check { border: 6px solid #17365d; }
        body.mode-selection .mode-picker-card .btn-main { color: #fff; background: linear-gradient(135deg, #214b7b, #17365d); }
        .page-shell { max-width: 1160px; min-height: calc(100vh - 76px); margin: 0 auto; padding: 34px 22px 112px; }
        .page-heading { display: flex; align-items: end; justify-content: space-between; gap: 20px; margin-bottom: 22px; }
        .page-heading-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
        .date-filter { display: flex; align-items: end; gap: 8px; }
        .date-filter label { margin: 0; white-space: nowrap; }
        .date-filter input { min-width: 150px; }
        .date-filter .button { white-space: nowrap; }
        .page-heading h1 { margin: 0 0 5px; font-size: clamp(1.4rem, 2.5vw, 1.9rem); letter-spacing: -.04em; }
        .page-heading p { margin: 0; color: #748198; font-size: .93rem; }
        .user-header { display: flex; align-items: center; gap: 11px; color: #66758b; font-size: .88rem; }
        .user-header form { margin: 0; }
        .btn-quiet { padding: 9px 12px; border: 1px solid #d8e3f6; color: #4c5e7a; background: linear-gradient(135deg, #fff, #e8f2ff 52%, #eee7ff); box-shadow: 0 6px 14px rgba(68,88,153,.12); }
        .card { min-width: 0; padding: 21px; border: 1px solid rgba(207, 220, 243, .95); border-radius: 18px; background: linear-gradient(145deg, rgba(255,255,255,.98) 0%, rgba(235,245,255,.96) 48%, rgba(245,237,255,.96) 100%); box-shadow: 0 12px 30px rgba(55, 82, 142, .11), 0 3px 8px rgba(65, 63, 132, .06); backdrop-filter: blur(10px); transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease, background .22s ease; }
        .card:hover { transform: translateY(-3px); border-color: rgba(183, 207, 245, .9); box-shadow: 0 17px 36px rgba(45, 73, 119, .12), 0 4px 10px rgba(45, 73, 119, .05); }
        .card h2, .card h3 { margin: 0; font-size: 1.02rem; }
        .overview-grid { display: grid; grid-template-columns: minmax(0, 1.5fr) repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
        .card-saldo { min-height: 182px; padding: 23px 25px; overflow: hidden; position: relative; color: #fff; border: 0; background: linear-gradient(120deg, #138aff 0%, #0869e8 48%, #5749d8 100%); box-shadow: 0 14px 32px rgba(24, 100, 220, .28), inset 0 1px 0 rgba(255,255,255,.22); }
        .card-saldo::after { content: ''; position: absolute; width: 210px; height: 210px; border-radius: 50%; border: 1px solid rgba(255,255,255,.11); right: -50px; top: -90px; box-shadow: 0 0 0 25px rgba(255,255,255,.035), 0 0 0 55px rgba(255,255,255,.025); }
        .balance-label { color: rgba(255,255,255,.83); font-size: .88rem; }
        .card-saldo h2 { margin: 7px 0 20px; font-size: clamp(1.7rem, 3vw, 2.25rem); letter-spacing: -.04em; }
        .balance-meta { display: flex; flex-wrap: wrap; gap: 10px 24px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,.23); font-size: .78rem; color: rgba(255,255,255,.9); }
        .balance-meta strong { color: #fff; }
        .metric-card { min-height: 182px; display: flex; flex-direction: column; justify-content: space-between; }
        .overview-grid .metric-card:nth-child(2) { background: linear-gradient(145deg, #fff 0%, #f0fff8 52%, #e4f8ee 100%); border-color: rgba(143, 219, 177, .58); }
        .overview-grid .metric-card:nth-child(3) { background: linear-gradient(145deg, #fff 0%, #fff6f1 52%, #ffebe4 100%); border-color: rgba(246, 185, 158, .6); }
        .main-column > .card:first-child, .content-grid > .card { background: linear-gradient(145deg, #fff 0%, #edf5ff 42%, #f2edff 100%); border-color: #d8e4fa; }
        .main-column > .card:nth-child(even) { background: linear-gradient(145deg, #fff 0%, #f0faff 48%, #e9f8f2 100%); border-color: #d3ebe6; }
        .side-column > .card:nth-child(1) { background: linear-gradient(145deg, #fff 0%, #eef7ff 48%, #f0edff 100%); border-color: #d9e4fa; }
        .side-column > .card:nth-child(2) { background: linear-gradient(145deg, #fff 0%, #effff7 48%, #e4f7ef 100%); border-color: #d2ecdf; }
        .side-column > .card:nth-child(3) { background: linear-gradient(145deg, #fff 0%, #fff8ed 48%, #fff0e8 100%); border-color: #f1e1d1; }
        .side-column > .card:nth-child(4) { background: linear-gradient(145deg, #fff 0%, #fff3f2 48%, #f8edff 100%); border-color: #eed9ea; }
        .metric-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; color: #76849a; font-size: .85rem; }
        .metric-icon { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 12px; background: linear-gradient(145deg, #dcecff, #cbdfff 52%, #dfd5ff); color: #0879f9; font-size: 1.12rem; box-shadow: inset 0 1px 2px rgba(255,255,255,.85), 0 6px 14px rgba(8,121,249,.16); }
        .overview-grid .metric-card:nth-child(2) .metric-icon { color: #07864f; background: linear-gradient(145deg, #dcf8e9, #c5f0d8 55%, #d8f5eb); box-shadow: 0 5px 12px rgba(16,153,96,.14); }
        .overview-grid .metric-card:nth-child(3) .metric-icon { color: #d45838; background: linear-gradient(145deg, #ffede1, #ffddcf 55%, #ffe8e1); box-shadow: 0 5px 12px rgba(212,88,56,.12); }
        .metric-value { margin-top: 13px; font-size: clamp(1.3rem, 2.2vw, 1.8rem); letter-spacing: -.04em; font-weight: 800; }
        .metric-note { margin-top: 6px; color: #8b97a9; font-size: .78rem; }
        .content-grid { display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(280px, .9fr); gap: 18px; align-items: start; }
        .main-column, .side-column { display: grid; gap: 18px; min-width: 0; }
        .section-heading { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 17px; }
        .section-heading p { margin: 5px 0 0; color: #8995a7; font-size: .82rem; }
        .period-form { display: flex; gap: 8px; align-items: center; }
        .report-filters { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)) auto; align-items: end; gap: 10px; margin: 0 0 14px; }
        .report-filters label { margin-top: 0; }
        .report-filters > div[hidden] { display: none; }
        label { display: block; margin: 12px 0 6px; color: #526176; font-size: .82rem; font-weight: 700; }
        input, select { width: 100%; min-height: 43px; padding: 10px 12px; border: 1px solid #d5e1f3; border-radius: 11px; background: linear-gradient(120deg, #fff 0%, #edf5ff 56%, #f2edff 100%); color: #25344a; font-size: .9rem; outline: none; box-shadow: inset 0 1px 2px rgba(25,54,92,.035), 0 5px 12px rgba(69,89,150,.08); transition: border-color .18s, box-shadow .18s, transform .18s, background .18s; }
        input:hover, select:hover { border-color: #b6ccef; }
        input:focus, select:focus { transform: translateY(-1px); border-color: #1682ff; box-shadow: 0 0 0 4px rgba(22,130,255,.12), 0 7px 15px rgba(22,130,255,.09); }
        .btn-main { color: #fff; background: linear-gradient(135deg, #1686ff, #0865e5); box-shadow: 0 7px 16px rgba(8, 105, 229, .22); }
        .btn-main:hover { background: linear-gradient(135deg, #0879f9, #0757cf); box-shadow: 0 10px 22px rgba(8, 105, 229, .3); }
        .btn-green { background: linear-gradient(135deg, #20bd7a, #07965d); box-shadow: 0 7px 16px rgba(16, 153, 96, .2); }
        .btn-green:hover { background: linear-gradient(135deg, #15aa6c, #087d4e); box-shadow: 0 10px 22px rgba(16,153,96,.28); }
        .btn-small { min-height: 40px; padding: 9px 14px; white-space: nowrap; }
        .btn-delete { padding: 7px 9px; color: #b82e43; background: linear-gradient(135deg, #fff1eb, #ffdcd9 55%, #f7def0); box-shadow: 0 5px 13px rgba(212, 68, 98, .16); font-size: .78rem; }
        .inline-form { display: flex; align-items: end; gap: 9px; }
        .inline-form > div { flex: 1; min-width: 0; }
        .inline-form button { flex: 0 0 auto; }
        .form-card h3 { margin-bottom: 2px; }
        .form-card .btn-main { width: 100%; margin-top: 14px; }
        .salary-summary-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .salary-summary-card { display: grid; align-content: center; gap: 9px; min-height: 125px; }
        .salary-summary-card > span { color: #718096; font-size: .84rem; }
        .salary-summary-card strong { color: #26364c; font-size: clamp(1.1rem, 2.2vw, 1.65rem); overflow-wrap: anywhere; }
        .salary-summary-primary { border-color: #bfdbfe; background: linear-gradient(135deg, #eef7ff, #edf0ff 62%, #f4efff); }
        .salary-content-grid { display: grid; grid-template-columns: minmax(0, 1.6fr) minmax(280px, .9fr); gap: 18px; align-items: start; }
        .salary-main-column, .salary-side-column { display: grid; gap: 18px; min-width: 0; }
        .salary-forms-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; align-items: start; }
        .salary-form-row { display: grid; grid-template-columns: 1.2fr .8fr; gap: 12px; }
        .salary-hint { margin: 8px 0 0; color: #718096; font-size: .78rem; line-height: 1.5; }
        .salary-chart-row { margin-top: 15px; }
        .salary-chart-label, .salary-budget-label { display: flex; justify-content: space-between; gap: 12px; color: #526176; font-size: .82rem; }
        .salary-chart-label strong { color: #34445b; font-size: .78rem; text-align: right; }
        .salary-chart-track, .salary-budget-track { height: 9px; margin-top: 7px; overflow: hidden; border-radius: 999px; background: #e9eef6; }
        .salary-chart-track > span, .salary-budget-track > span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #1686ff, #7651db); }
        .salary-budget-list { display: grid; gap: 15px; margin-top: 20px; }
        .salary-budget-item { min-width: 0; }
        .salary-budget-label { align-items: baseline; }
        .salary-budget-label strong { color: #34445b; }
        .salary-budget-label span { color: #718096; font-size: .75rem; text-align: right; }
        .salary-budget-track.warning > span { background: linear-gradient(90deg, #f2b63f, #ed8b32); }
        .salary-budget-track.danger > span { background: linear-gradient(90deg, #ef6b5d, #d63e58); }
        .salary-budget-warning { display: block; margin-top: 5px; color: #9a6814; font-size: .74rem; font-weight: 700; }
        .salary-budget-warning.danger { color: #bb3444; }
        .salary-history-card { min-width: 0; }
        .chart-wrap { height: 265px; position: relative; padding: 12px; border: 1px solid rgba(198,216,246,.8); border-radius: 15px; background: linear-gradient(135deg, rgba(255,255,255,.8), rgba(226,241,255,.72) 52%, rgba(238,229,255,.72)); box-shadow: inset 0 1px 3px rgba(42,73,131,.05), 0 6px 15px rgba(64,83,145,.07); }
        .chart-wrap-small { height: 230px; position: relative; padding: 12px; border: 1px solid rgba(198,216,246,.8); border-radius: 15px; background: linear-gradient(135deg, rgba(255,255,255,.8), rgba(229,248,241,.74) 52%, rgba(237,232,255,.72)); box-shadow: inset 0 1px 3px rgba(42,73,131,.05), 0 6px 15px rgba(64,83,145,.07); }
        .chart-empty { display: none; height: 100%; min-height: 170px; place-items: center; color: #8995a7; font-size: .9rem; }
        .insight { padding: 13px 15px; border: 1px solid #dcecff; border-radius: 12px; background: linear-gradient(120deg, #f0f8ff, #f4f1ff 55%, #edfbf5); color: #31577e; font-size: .86rem; line-height: 1.5; box-shadow: 0 7px 18px rgba(55,102,170,.08); }
        .insight.warning { border-color: #f8e4c2; background: linear-gradient(120deg, #fffaf0, #fff2e8 55%, #fff0f1); color: #8a5b15; }
        .daily-budget-card { margin-bottom: 18px; border-color: #bdebd2; background: linear-gradient(130deg, #f2fff7 0%, #e3f8ed 52%, #edf8ff 100%); }
        .daily-budget-warning { border-color: #f1d594; background: linear-gradient(130deg, #fffaf0 0%, #fff1d8 52%, #fff4ea 100%); }
        .daily-budget-danger { border-color: #f2b8bb; background: linear-gradient(130deg, #fff5f3 0%, #ffe6e5 52%, #fff0f5 100%); }
        .daily-budget-heading { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 17px; }
        .daily-budget-heading h2 { margin: 4px 0 0; font-size: 1.1rem; }
        .daily-budget-eyebrow { color: #60758d; font-size: .68rem; font-weight: 800; letter-spacing: .1em; }
        .daily-budget-status { display: inline-flex; align-items: center; min-height: 30px; padding: 5px 10px; border-radius: 999px; color: #167044; background: rgba(32,166,106,.12); font-size: .76rem; font-weight: 800; white-space: nowrap; }
        .daily-budget-warning .daily-budget-status { color: #93600b; background: rgba(226,164,42,.17); }
        .daily-budget-danger .daily-budget-status { color: #ae3440; background: rgba(220,75,85,.13); }
        .daily-budget-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 11px; }
        .daily-budget-stat { display: grid; gap: 5px; min-width: 0; padding: 13px; border: 1px solid rgba(255,255,255,.75); border-radius: 12px; background: rgba(255,255,255,.56); }
        .daily-budget-stat > span { color: #64748b; font-size: .76rem; line-height: 1.4; }
        .daily-budget-stat strong { color: #26364c; font-size: clamp(1rem, 2.1vw, 1.35rem); overflow-wrap: anywhere; }
        .daily-budget-stat small { color: #75839a; font-size: .72rem; line-height: 1.4; }
        .schedule-setup-card { max-width: 560px; }
        .schedule-description, .schedule-hint { color: #718096; font-size: .88rem; line-height: 1.55; }
        .schedule-setup-card .btn-main { width: 100%; margin-top: 12px; }
        body.dark-mode .daily-budget-card { border-color: #286c4d; background: linear-gradient(130deg, #1d382c, #203a35 52%, #202e3d); }
        body.dark-mode .daily-budget-warning { border-color: #78612f; background: linear-gradient(130deg, #3c3321, #423523 52%, #3b2e2a); }
        body.dark-mode .daily-budget-danger { border-color: #784047; background: linear-gradient(130deg, #3d292d, #482b36 52%, #3d293d); }
        body.dark-mode .daily-budget-stat { border-color: rgba(255,255,255,.08); background: rgba(14,22,35,.28); }
        body.dark-mode .daily-budget-stat > span, body.dark-mode .daily-budget-stat small, body.dark-mode .schedule-description, body.dark-mode .schedule-hint, body.dark-mode .daily-budget-eyebrow { color: #b1bdce; }
        body.dark-mode .daily-budget-stat strong, body.dark-mode .daily-budget-heading h2 { color: #e5eaf2; }
        .item-transaksi { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 12px; margin: 0 -12px; border-bottom: 1px solid #edf1f6; border-radius: 12px; transition: background .18s ease, transform .18s ease, box-shadow .18s ease; }
        .item-transaksi:hover { transform: translateX(3px); background: linear-gradient(100deg, rgba(230,244,255,.9), rgba(239,239,255,.75) 55%, rgba(242,255,248,.75)); box-shadow: 0 5px 14px rgba(45,73,119,.07); }
        .item-transaksi:last-child { padding-bottom: 0; border-bottom: 0; }
        .transaction-name { color: #26364c; font-size: .9rem; font-weight: 700; }
        .transaction-meta { display: block; margin-top: 5px; color: #8a96a8; font-size: .78rem; }
        .transaction-actions { display: flex; align-items: center; gap: 10px; white-space: nowrap; }
        .pemasukan-text { color: #0b9a61; font-weight: 800; font-size: .9rem; }
        .pengeluaran-text { color: #df5555; font-weight: 800; font-size: .9rem; }
        .transaction-actions form { margin: 0; }
        .pagination { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-top: 15px; color: #748198; font-size: .85rem; }
        .pagination a { color: #0879f9; text-decoration: none; font-weight: 700; }
        .text-link { display: inline-block; margin-top: 16px; color: #0879f9; text-decoration: none; font-size: .88rem; font-weight: 700; }
        .text-link:hover { text-decoration: underline; }
        .pagination nav { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .pagination nav > div:first-child { color: #748198; font-size: .85rem; }
        .pagination nav > div:last-child { display: flex; gap: 6px; }
        .pagination nav a, .pagination nav span[aria-current="page"] span, .pagination nav span[aria-disabled="true"] span { display: grid; min-width: 34px; height: 34px; place-items: center; padding: 0 9px; border: 1px solid #d8e3f6; border-radius: 8px; background: linear-gradient(135deg, #fff, #edf4ff 60%, #f0ebff); box-shadow: 0 4px 10px rgba(64,83,145,.1); }
        .pagination nav span[aria-current="page"] span { color: #fff; border-color: #0879f9; background: linear-gradient(135deg, #1686ff, #0865e5 55%, #654bd7); box-shadow: 0 5px 12px rgba(8,105,229,.25); }
        .pagination nav span[aria-disabled="true"] span { color: #a7b0bd; }
        .pesan-kosong { padding: 20px 0; text-align: center; color: #8995a7; font-size: .9rem; }
        .alert, .errors { padding: 12px 15px; border-radius: 10px; margin-bottom: 17px; font-size: .9rem; }
        .alert { border: 1px solid rgba(94, 196, 131, .22); background: linear-gradient(115deg, #effcf4, #e5f7ee 55%, #e9f7ff); color: #23734a; box-shadow: 0 7px 18px rgba(35,115,74,.08); }
        .errors { border: 1px solid rgba(235, 120, 120, .2); background: linear-gradient(115deg, #fff5f3, #ffebea 55%, #fff0f6); color: #a52922; box-shadow: 0 7px 18px rgba(165,41,34,.07); }
        .danger-zone { border-color: #f2cccc; }
        .danger-zone h2 { color: #b43232; }
        .divider { text-align: center; margin: 16px 0 8px; color: #777; font-size: .85rem; }
        .toggle-auth { text-align: center; font-size: .9rem; margin: 16px 0 0; }
        .toggle-auth a { color: #007aff; }
        .password-field { position: relative; }
        .password-field > input { padding-right: 50px; }
        .password-toggle { position: absolute; top: 50%; right: 5px; display: grid; width: 38px; height: 38px; place-items: center; padding: 8px; transform: translateY(-50%); border: 0; border-radius: 9px; color: #68778d; background: transparent; box-shadow: none; }
        .password-toggle:hover { transform: translateY(-50%); color: #0879f9; background: rgba(8,121,249,.08); box-shadow: none; }
        .password-toggle svg { width: 20px; height: 20px; }
        .password-icon-hidden, .password-toggle[aria-pressed="true"] .password-icon-visible { display: none; }
        .password-toggle[aria-pressed="true"] .password-icon-hidden { display: block; }
        body.dark-mode .password-toggle { color: #c3cedd; }
        .auth-shell { display: flex; flex-direction: column; justify-content: center; width: min(100%, 480px); min-height: 100vh; padding: 36px 22px; }
        .mode-picker { width: min(100%, 570px); margin: auto; }
        .mode-picker-brand { display: flex; align-items: center; justify-content: center; gap: 11px; margin-bottom: 20px; color: #172b4d; font-size: 1.5rem; font-weight: 800; }
        .mode-picker-brand img { width: 46px; height: 46px; border-radius: 13px; object-fit: cover; }
        .mode-picker-card { padding: clamp(22px, 5vw, 32px); }
        .mode-picker-eyebrow { color: #0879f9; font-size: .72rem; font-weight: 800; letter-spacing: .1em; }
        .mode-picker-card h1 { margin: 8px 0; font-size: clamp(1.35rem, 5vw, 1.8rem); }
        .mode-picker-card > p, .salary-placeholder p, .salary-settings-note p { color: #718096; line-height: 1.6; }
        .mode-options { display: grid; gap: 12px; margin: 22px 0 8px; padding: 0; border: 0; }
        .mode-settings-card .mode-options { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin: 18px 0 0; }
        .mode-settings-card .mode-choice-card { min-height: 160px; padding: 16px; }
        .mode-settings-hint { margin: 10px 0 0; color: #718096; font-size: .78rem; }
        body.salary-mode .mode-choice > input:checked + .mode-choice-card { border-color: #059669; background: #eff8f3; box-shadow: 0 0 0 3px rgba(5,150,105,.1), 0 10px 24px rgba(5,150,105,.08); }
        body.salary-mode .mode-choice-icon { color: #047857; background: #e4f5eb; }
        body.salary-mode .mode-choice > input:checked + .mode-choice-card .mode-choice-check { border-color: #059669; }
        body.dark-mode .mode-settings-card .mode-choice-card { border-color: #475569; background: linear-gradient(145deg, #273449, #2d354d); }
        body.dark-mode .mode-settings-card .mode-choice-card strong { color: #e5eaf2; }
        body.dark-mode .mode-settings-card .mode-choice-card small { color: #aab6c8; }
        body.dark-mode .mode-settings-card .mode-choice-icon { color: #d6e6fa; background: #344a66; }
        body.dark-mode .mode-settings-card .mode-choice-check { border-color: #718096; background: #1f2937; }
        body.dark-mode .mode-settings-hint { color: #aab6c8; }
        body.dark-mode.salary-mode .mode-choice > input:checked + .mode-choice-card { border-color: #10b981; background: #203a30; }
        body.dark-mode.salary-mode .mode-choice > input:checked + .mode-choice-card .mode-choice-check { border-color: #10b981; }
        .mode-option { display: flex; align-items: flex-start; gap: 12px; margin: 0; padding: 16px; border: 1px solid #d9e4f4; border-radius: 13px; background: linear-gradient(130deg, #fff, #eef6ff 58%, #f4efff); cursor: pointer; }
        .mode-option:has(input:checked) { border-color: #1682ff; box-shadow: 0 0 0 3px rgba(22,130,255,.12); }
        .mode-option input { width: 18px; min-height: 18px; margin: 2px 0 0; flex: 0 0 auto; accent-color: #0879f9; }
        .mode-option-copy { display: grid; gap: 4px; }
        .mode-option-copy strong { color: #26364c; }
        .mode-option-copy small { color: #718096; font-size: .8rem; font-weight: 400; line-height: 1.5; }
        .mode-picker-card .btn-main { width: 100%; min-height: 46px; margin-top: 12px; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .mode-settings-card, .salary-settings-note { max-width: 620px; margin: 18px 0; }
        .mode-settings-card { margin-top: 0; }
        .mode-settings-card .btn-main { width: 100%; margin-top: 12px; }
        .salary-placeholder { display: grid; justify-items: center; max-width: 650px; margin: 56px auto; padding: clamp(24px, 6vw, 44px); text-align: center; }
        .salary-placeholder-mark { display: grid; width: 64px; height: 64px; place-items: center; border-radius: 20px; color: #fff; background: linear-gradient(135deg, #1686ff, #6850db); box-shadow: 0 10px 24px rgba(68,75,184,.25); font-size: 1.35rem; font-weight: 800; }
        .salary-placeholder h2 { margin: 18px 0 0; }
        .salary-placeholder .button { margin-top: 12px; }
        body.dark-mode .mode-picker-brand { color: #e5eaf2; }
        body.dark-mode .mode-option { border-color: #475569; background: linear-gradient(130deg, #273449, #2d354d 58%, #342e4b); }
        body.dark-mode .mode-option-copy strong { color: #e5eaf2; }
        body.dark-mode .mode-option-copy small, body.dark-mode .mode-picker-card > p, body.dark-mode .salary-placeholder p, body.dark-mode .salary-settings-note p { color: #aab6c8; }
        .auth-brand { display: flex; align-items: center; justify-content: center; gap: 12px; margin: 0 0 22px; color: #172b4d; font-size: clamp(1.45rem, 5vw, 1.8rem); font-weight: 800; letter-spacing: -.04em; text-align: center; }
        .auth-brand img { width: 52px; height: 52px; flex: 0 0 auto; border-radius: 14px; object-fit: cover; box-shadow: 0 7px 18px rgba(8,105,229,.18); }
        .auth-card { width: 100%; padding: clamp(20px, 5vw, 30px); }
        .auth-card h3 { margin-bottom: 17px; }
        .auth-form { display: grid; gap: 0; }
        .auth-field { min-width: 0; margin-bottom: 9px; }
        .auth-field label { margin: 0 0 6px; }
        .auth-card label { line-height: 1.45; }
        .auth-card input:not([type="checkbox"]) { min-height: 48px; }
        .auth-field input[data-state="valid"] { border-color: #20a66a; box-shadow: 0 0 0 3px rgba(32,166,106,.1); }
        .auth-field input[data-state="invalid"] { border-color: #dc4b55; box-shadow: 0 0 0 3px rgba(220,75,85,.1); }
        .field-status, .password-rules { margin: 6px 0 0; color: #728096; font-size: .78rem; line-height: 1.45; }
        .password-rules { display: grid; gap: 4px; padding: 0; list-style: none; }
        .password-rules li::before { display: inline-grid; width: 17px; height: 17px; place-items: center; margin-right: 7px; border: 1px solid #a5afbd; border-radius: 50%; color: #728096; content: '·'; font-size: 1rem; font-weight: 800; vertical-align: -2px; }
        .field-status[data-state="valid"], .password-rules [data-state="valid"] { color: #14804f; }
        .field-status[data-state="invalid"], .password-rules [data-state="invalid"] { color: #bf3441; }
        .password-rules [data-state="valid"]::before { border-color: #20a66a; color: #fff; background: #20a66a; content: '✓'; font-size: .7rem; }
        .password-rules [data-state="invalid"]::before { border-color: #dc4b55; color: #fff; background: #dc4b55; content: '×'; font-size: .85rem; }
        .auth-card .btn-main { width: 100%; min-height: 46px; margin-top: 16px; }
        .auth-card .btn-quiet { width: 100%; min-height: 44px; margin-top: 10px; }
        .auth-card > p:not(.toggle-auth) { line-height: 1.55; }
        .auth-code { text-align: center; font-size: 1.25rem !important; font-weight: 800; letter-spacing: .35em; }
        body.dark-mode .auth-brand { color: #e5eaf2; }
        body.dark-mode .field-status, body.dark-mode .password-rules { color: #aab6c8; }
        body.dark-mode .field-status[data-state="valid"], body.dark-mode .password-rules [data-state="valid"] { color: #62d69a; }
        body.dark-mode .field-status[data-state="invalid"], body.dark-mode .password-rules [data-state="invalid"] { color: #ff858b; }
        @media (max-width: 560px) {
            .page-shell.auth-shell { min-height: 100svh; padding: 28px 16px; }
            .auth-brand { margin-bottom: 18px; }
            .auth-brand img { width: 46px; height: 46px; border-radius: 12px; }
            .auth-card { border-radius: 16px; }
            .auth-card .btn-main, .auth-card .btn-quiet { white-space: normal; }
            .mode-picker { margin: auto; }
            .mode-option { padding: 14px; }
            .daily-budget-grid { grid-template-columns: 1fr; }
            body.kos-mode .daily-budget-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            body.kos-mode .daily-budget-stat:last-child { grid-column: 1 / -1; }
            .daily-budget-heading { align-items: flex-start; }
            .daily-budget-status { font-size: .68rem; }
        }
        @media (max-width: 380px) {
            body.kos-mode .daily-budget-grid { grid-template-columns: 1fr; }
            body.kos-mode .daily-budget-stat:last-child { grid-column: auto; }
        }
        body.dark-mode { color: #e5eaf2; background: linear-gradient(135deg, #101827 0%, #17152c 48%, #102522 100%); }
        body.dark-mode::before { background: radial-gradient(circle, rgba(38, 112, 210, .2), rgba(38, 112, 210, 0) 70%); }
        body.dark-mode::after { background: radial-gradient(circle, rgba(117, 77, 218, .18), rgba(117, 77, 218, 0) 70%); }
        body.dark-mode .topbar { background: rgba(24, 32, 47, .84); border-color: rgba(55,65,81,.8); }
        body.dark-mode .card, body.dark-mode .menu-panel, body.dark-mode .profile-panel { background: rgba(31, 41, 55, .92); border-color: #374151; box-shadow: 0 12px 30px rgba(0,0,0,.2); }
        body.dark-mode .menu-panel, body.dark-mode .profile-panel { background: linear-gradient(145deg, #1f2937, #24243a 58%, #1e3031); }
        body.dark-mode .card:not(.card-saldo) { background: linear-gradient(145deg, rgba(31,41,55,.96), rgba(35,42,61,.94) 58%, rgba(29,48,49,.92)); }
        body.dark-mode .main-column > .card:first-child, body.dark-mode .content-grid > .card { background: linear-gradient(145deg, #202b3d, #242b45 55%, #302947); }
        body.dark-mode .main-column > .card:nth-child(even) { background: linear-gradient(145deg, #1f3034, #233b3c 55%, #263546); }
        body.dark-mode .side-column > .card:nth-child(1) { background: linear-gradient(145deg, #202b3d, #263149 55%, #302947); }
        body.dark-mode .side-column > .card:nth-child(2) { background: linear-gradient(145deg, #1f332f, #203b36 55%, #1d3930); }
        body.dark-mode .side-column > .card:nth-child(3) { background: linear-gradient(145deg, #382f2b, #403238 55%, #392b3a); }
        body.dark-mode .side-column > .card:nth-child(4) { background: linear-gradient(145deg, #382a2c, #402d35 55%, #382a3d); }
        body.dark-mode .overview-grid .metric-card:nth-child(2) { background: linear-gradient(145deg, #1f332f, #203b36 55%, #1d3930); border-color: #315b49; }
        body.dark-mode .overview-grid .metric-card:nth-child(3) { background: linear-gradient(145deg, #382a2c, #402d35 55%, #382a3d); border-color: #69434b; }
        body.dark-mode .card:hover { border-color: #52627a; box-shadow: 0 18px 38px rgba(0,0,0,.3); }
        body.dark-mode .brand, body.dark-mode .transaction-name, body.dark-mode .menu-panel a, body.dark-mode .profile-links a { color: #e5eaf2; }
        body.dark-mode .page-heading p, body.dark-mode .section-heading p, body.dark-mode .transaction-meta, body.dark-mode .metric-top, body.dark-mode .profile-email, body.dark-mode .user-header { color: #aab6c8; }
        body.dark-mode .menu-button, body.dark-mode .btn-quiet, body.dark-mode input, body.dark-mode select { color: #e5eaf2; background: #273449; border-color: #475569; }
        body.dark-mode .menu-button span { background: #e5eaf2; }
        body.dark-mode .profile-toggle { color: #b8d8ff; background: #243f60; border-color: #35577e; }
        body.dark-mode .theme-fab button { background: linear-gradient(130deg, #304e88, #55418e 58%, #793f7e); box-shadow: 0 12px 28px rgba(0,0,0,.38), inset 0 1px 0 rgba(255,255,255,.2); }
        body.dark-mode .profile-links a:hover, body.dark-mode .menu-panel a:hover, body.dark-mode .menu-panel a.active { color: #8fc2ff; background: #273b54; }
        body.dark-mode .item-transaksi { border-color: #374151; }
        body.dark-mode .item-transaksi:hover { background: linear-gradient(90deg, rgba(39,59,84,.85), rgba(55,45,82,.48)); }
        body.dark-mode .salary-summary-card > span, body.dark-mode .salary-hint, body.dark-mode .salary-chart-label, body.dark-mode .salary-budget-label span { color: #aab6c8; }
        body.dark-mode .salary-summary-card strong, body.dark-mode .salary-chart-label strong, body.dark-mode .salary-budget-label strong { color: #e5eaf2; }
        body.dark-mode .salary-summary-primary { border-color: #3d4e78; background: linear-gradient(135deg, #202d43, #292d4b 62%, #342d49); }
        body.dark-mode .salary-chart-track, body.dark-mode .salary-budget-track { background: #364256; }
        body.dark-mode.salary-mode { color: #e5eaf2; background: #101c19; }
        body.dark-mode.salary-mode::before { background: radial-gradient(circle, rgba(16,185,129,.13), rgba(16,185,129,0) 70%); }
        body.dark-mode.salary-mode::after { background: radial-gradient(circle, rgba(6,95,70,.16), rgba(6,95,70,0) 70%); }
        body.dark-mode.salary-mode .topbar { background: rgba(20,36,31,.9); border-color: #29463a; }
        body.dark-mode.salary-mode .brand { color: #a7f3d0; }
        body.dark-mode.salary-mode .menu-button { background: #203a30; }
        body.dark-mode.salary-mode .menu-button span { background: #d1fae5; }
        body.dark-mode.salary-mode .menu-panel { border-color: #315243; background: linear-gradient(145deg, #1c3029, #20392f); }
        body.dark-mode.salary-mode .menu-panel a:hover, body.dark-mode.salary-mode .menu-panel a.active { color: #a7f3d0; background: #29483a; }
        body.dark-mode.salary-mode .card { border-color: #2b4439; background: #1b2b24; box-shadow: 0 8px 24px rgba(0,0,0,.16); }
        body.dark-mode.salary-mode .card:hover { border-color: #3e6653; }
        body.dark-mode.salary-mode .page-heading h1, body.dark-mode.salary-mode .card h2, body.dark-mode.salary-mode .card h3 { color: #e5eaf2; }
        body.dark-mode.salary-mode .page-heading p, body.dark-mode.salary-mode .section-heading p, body.dark-mode.salary-mode .salary-summary-card > span, body.dark-mode.salary-mode .salary-hint { color: #aab6c8; }
        body.dark-mode.salary-mode .salary-summary-card strong { color: #6ee7b7; }
        body.dark-mode.salary-mode .salary-expense-card strong { color: #f87171; }
        body.dark-mode.salary-mode .salary-summary-primary { border-color: #2b4439; background: #1b2b24; }
        body.dark-mode.salary-mode .btn-quiet { color: #a7f3d0; border-color: #3e6653; background: #203a30; }
        body.dark-mode.salary-mode .btn-quiet:hover { background: #29483a; }
        body.dark-mode.salary-mode input, body.dark-mode.salary-mode select { color: #e5eaf2; border-color: #3b5548; background: #20342b; }
        body.dark-mode.salary-mode .bottom-nav { border-color: #2b4439; background: rgba(27,43,36,.96); box-shadow: 0 12px 34px rgba(0,0,0,.38), inset 0 2px 0 rgba(255,255,255,.06); }
        body.dark-mode.salary-mode .bottom-nav a { color: #aab6c8; }
        body.dark-mode.salary-mode .bottom-nav a.active, body.dark-mode.salary-mode .bottom-nav a:hover { color: #6ee7b7; }
        body.dark-mode.salary-mode .bottom-nav a.active::before { border-color: #3b6651; background: radial-gradient(circle at 35% 25%, #29483a, #203a30 60%, #1d342a); }
        body.dark-mode.salary-mode .bottom-nav a.active::after { background: #10b981; }
        body.dark-mode.salary-mode .theme-fab button { background: linear-gradient(135deg, #064e3b, #065f46); }
        body.dark-mode.salary-mode .salary-chart-label, body.dark-mode.salary-mode .salary-chart-label strong, body.dark-mode.salary-mode .salary-budget-label strong, body.dark-mode.salary-mode .salary-budget-label span { color: #d1fae5; }
        body.dark-mode.salary-mode .salary-chart-track, body.dark-mode.salary-mode .salary-budget-track { background: #30463b; }
        body.dark-mode.salary-mode .salary-chart-track > span, body.dark-mode.salary-mode .salary-budget-track > span { background: linear-gradient(90deg, #10b981, #059669); }
        body.dark-mode.kos-mode { color: #e5eaf2; background: #142237; }
        body.dark-mode.kos-mode .topbar { background: rgba(22,37,58,.94); border-color: #30435d; }
        body.dark-mode.kos-mode .brand { color: #d6e6fa; }
        body.dark-mode.kos-mode .menu-button { background: #243a57; }
        body.dark-mode.kos-mode .menu-button span { background: #d6e6fa; }
        body.dark-mode.kos-mode .menu-panel { border-color: #394d67; background: #1d304a; }
        body.dark-mode.kos-mode .menu-panel a { color: #d6e6fa; }
        body.dark-mode.kos-mode .menu-panel a:hover, body.dark-mode.kos-mode .menu-panel a.active { color: #fff; background: #304968; }
        body.dark-mode.kos-mode .page-shell { background: #1c293a; box-shadow: 0 0 32px rgba(0,0,0,.12); }
        body.dark-mode.kos-mode .card { border-color: #35465b; background: #243449; }
        body.dark-mode.kos-mode .page-heading h1, body.dark-mode.kos-mode .card h2, body.dark-mode.kos-mode .card h3 { color: #e5eaf2; }
        body.dark-mode.kos-mode .page-heading p, body.dark-mode.kos-mode .section-heading p, body.dark-mode.kos-mode .metric-top, body.dark-mode.kos-mode .schedule-description, body.dark-mode.kos-mode .schedule-hint { color: #aab6c8; }
        body.dark-mode.kos-mode .btn-quiet { color: #d6e6fa; border-color: #6680a0; background: #243a57; }
        body.dark-mode.kos-mode .bottom-nav { border-color: #35465b; background: rgba(31,47,68,.96); }
        body.dark-mode.kos-mode .bottom-nav a { color: #aab6c8; }
        body.dark-mode.kos-mode .bottom-nav a.active, body.dark-mode.kos-mode .bottom-nav a:hover { color: #d6e6fa; }
        body.dark-mode.kos-mode .bottom-nav a.active::before { border-color: #6680a0; background: radial-gradient(circle at 35% 25%, #385574, #2a425f 60%, #243a57); }
        body.dark-mode.kos-mode .bottom-nav a.active::after { background: #9db9da; }
        body.dark-mode.kos-mode .bottom-nav-ripple { background: rgba(157,185,218,.2); }
        body.dark-mode.kos-mode .daily-budget-card { border-color: #394e69; background: radial-gradient(ellipse at 100% 0%, rgba(76,108,146,.25), transparent 48%), linear-gradient(135deg, #25364b, #202f43 58%, #1d2a3c); }
        body.dark-mode.kos-mode .daily-budget-warning { border-color: #6f5b37; background: radial-gradient(ellipse at 100% 0%, rgba(153,119,59,.2), transparent 48%), linear-gradient(135deg, #383324, #332f29 58%, #2d2b2d); }
        body.dark-mode.kos-mode .daily-budget-danger { border-color: #70434c; background: radial-gradient(ellipse at 100% 0%, rgba(157,76,86,.2), transparent 48%), linear-gradient(135deg, #38272e, #342630 58%, #2d2634); }
        body.dark-mode.kos-mode .daily-budget-eyebrow { color: #a9c2df; }
        body.dark-mode.kos-mode .daily-budget-heading h2 { color: #e5eaf2; }
        body.dark-mode.kos-mode .daily-budget-stat { border-color: #3d5068; background: linear-gradient(145deg, rgba(39,56,77,.94), rgba(31,46,65,.92)); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
        body.dark-mode.kos-mode .daily-budget-stat::before { background: linear-gradient(180deg, #91accb, #54769e); }
        body.dark-mode.kos-mode .daily-budget-stat:nth-child(2) { background: linear-gradient(145deg, #2b4058, #26384e); }
        body.dark-mode.kos-mode .daily-budget-stat:nth-child(3) { border-color: #63424a; background: linear-gradient(145deg, #3a2d35, #342832); }
        body.dark-mode.kos-mode .daily-budget-stat:nth-child(3)::before { background: linear-gradient(180deg, #e19b93, #bc6667); }
        body.dark-mode.kos-mode .daily-budget-stat strong { color: #d6e6fa; }
        body.dark-mode.kos-mode .daily-budget-stat:nth-child(3) strong { color: #ffb4ab; }
        body.dark-mode.kos-mode .card-saldo { background: radial-gradient(ellipse at 100% 0%, rgba(255,255,255,.12), transparent 46%), linear-gradient(125deg, #315173, #263f5d 52%, #1f344e); }
        body.dark-mode.kos-mode .theme-fab button { background: linear-gradient(135deg, #294b70, #17365d); }
        body.dark-mode.kos-mode input, body.dark-mode.kos-mode select { color: #e5eaf2; border-color: #465b74; background: #24364d; }
        body.dark-mode.mode-selection .mode-picker-brand { color: #d6e6fa; }
        body.dark-mode.mode-selection .mode-picker-card { border-color: #35465b; background: #243449; }
        body.dark-mode.mode-selection .mode-picker-eyebrow { color: #9db9da; }
        body.dark-mode.mode-selection .mode-picker-card h1, body.dark-mode.mode-selection .mode-choice-card strong { color: #e5eaf2; }
        body.dark-mode.mode-selection .mode-picker-card > p, body.dark-mode.mode-selection .mode-choice-card small { color: #aab6c8; }
        body.dark-mode.mode-selection .mode-choice-card { border-color: #465b74; background: linear-gradient(145deg, #24364d, #293b52); }
        body.dark-mode.mode-selection .mode-choice > input:checked + .mode-choice-card { border-color: #9db9da; background: #2b425e; }
        body.dark-mode.mode-selection .mode-choice-icon { color: #d6e6fa; background: #334b68; }
        body.dark-mode.mode-selection .mode-choice-check { border-color: #6680a0; background: #24364d; }
        body.dark-mode.mode-selection .mode-choice > input:checked + .mode-choice-card .mode-choice-check { border-color: #9db9da; }
        body.dark-mode input, body.dark-mode select { background: linear-gradient(135deg, #273449, #2d354d 58%, #293a3b); }
        body.dark-mode .chart-wrap { border-color: #46536b; background: linear-gradient(135deg, #202c3d, #26334b 55%, #302b48); box-shadow: inset 0 1px 4px rgba(0,0,0,.18), 0 7px 17px rgba(0,0,0,.16); }
        body.dark-mode .chart-wrap-small { border-color: #46536b; background: linear-gradient(135deg, #20332f, #263b3e 55%, #302b48); box-shadow: inset 0 1px 4px rgba(0,0,0,.18), 0 7px 17px rgba(0,0,0,.16); }
        body.dark-mode .btn-quiet, body.dark-mode .pagination nav a, body.dark-mode .pagination nav span[aria-disabled="true"] span { background: linear-gradient(135deg, #273449, #303750 60%, #38304c); border-color: #48556d; }
        body.dark-mode .pagination nav span[aria-current="page"] span { background: linear-gradient(135deg, #1686ff, #0865e5 55%, #654bd7); }
        body.dark-mode .metric-icon { color: #8fc2ff; background: #273b54; }
        body.dark-mode .overview-grid .metric-card:nth-child(2) .metric-icon { color: #91e7b5; background: linear-gradient(145deg, #244b3e, #285444); }
        body.dark-mode .overview-grid .metric-card:nth-child(3) .metric-icon { color: #ffb9a2; background: linear-gradient(145deg, #573a3b, #64424c); }
        body.dark-mode .alert { color: #b6f0ca; background: linear-gradient(135deg, #173b2b, #1d4734); }
        body.dark-mode .errors { color: #ffc4c4; background: linear-gradient(135deg, #472828, #532c36); }
        body.dark-mode .danger-zone { border-color: #744141; }
        body.dark-mode .danger-zone h2 { color: #ff9e9e; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; animation-iteration-count: 1 !important; } }
        hr { border: 0; border-top: 1px solid rgba(255,255,255,.45); margin: 12px 0; }
        @media (max-width: 840px) { .overview-grid { grid-template-columns: 1fr 1fr; } .card-saldo { grid-column: 1 / -1; } .content-grid, .salary-content-grid { grid-template-columns: 1fr; } .side-column { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 560px) { .topbar { height: 64px; padding: 0 16px; } .page-shell { padding: 22px 14px 124px; } .theme-fab { right: 16px; bottom: 108px; } .theme-fab button { width: 42px; height: 42px; } .bottom-nav { bottom: max(12px, env(safe-area-inset-bottom)); width: calc(100% - 24px); min-height: 76px; padding: 8px 10px; border-radius: 29px; grid-template-columns: minmax(0, 1fr) 0 minmax(0, 1fr); } .bottom-nav-home { width: 64px; } .bottom-nav a { font-size: .64rem; } .bottom-nav svg { width: 20px; height: 20px; } .page-heading { align-items: flex-start; flex-direction: column; gap: 12px; } .page-heading-actions { width: 100%; flex-wrap: wrap; justify-content: flex-start; } .date-filter { flex: 1 1 100%; } .date-filter input { flex: 1; min-width: 0; } .overview-grid { gap: 10px; } .card { padding: 17px; border-radius: 14px; } .card-saldo { min-height: 170px; padding: 20px; } .balance-meta { gap: 9px 15px; font-size: .72rem; } .metric-card { min-height: 145px; } .metric-icon { width: 32px; height: 32px; border-radius: 10px; } .metric-value { font-size: 1.2rem; } .side-column, .salary-forms-grid, .salary-form-row { grid-template-columns: 1fr; } .salary-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; } .salary-summary-card { min-height: 100px; } .salary-balance-card { grid-column: 1 / -1; min-height: 145px; padding: 19px; } .salary-balance-card strong { font-size: 1.75rem; } .mode-options { grid-template-columns: 1fr; } .mode-choice-card { min-height: 150px; padding: 17px; } .section-heading { align-items: flex-start; flex-direction: column; } .period-form { width: 100%; } .period-form select { flex: 1; } .report-filters { grid-template-columns: 1fr; } .report-filters button { width: 100%; } .chart-wrap { height: 230px; } .inline-form { display: block; } .inline-form button { width: 100%; margin-top: 9px; } .transaction-actions { flex-direction: column; align-items: flex-end; gap: 6px; } .salary-budget-label { align-items: flex-start; flex-direction: column; gap: 3px; } .salary-budget-label span { text-align: left; } }
        body.dark-mode .bottom-nav { border-color: #475569; background: rgba(31,41,55,.94); box-shadow: 0 12px 34px rgba(0,0,0,.38), inset 0 2px 0 rgba(255,255,255,.08); }
        body.dark-mode .bottom-nav a { color: #aab6c8; }
        body.dark-mode .bottom-nav a.active { color: #8fc2ff; }
        body.dark-mode .bottom-nav a.active::before { background: #273b54; }
        body.dark-mode .bottom-nav-ripple { background: rgba(115,175,255,.22); }
        @media (prefers-reduced-motion: reduce) { .bottom-nav a, .bottom-nav svg { transition-duration: .01ms; } .bottom-nav a.active { animation: none; } .bottom-nav-ripple { display: none; } }
        body.dark-mode .menu-panel .menu-logout { border-color: #475569; }

        /* ==========================================================================
           Cartoon Walking -> Crash -> Brand Reveal -> Wobble-Grow-Fade Dissolve
           Revisi:
           1. Durasi lebih rileks & nyaman dinikmati
           2. Posisi benturan presisi (logo menimpa langsung di atas karakter)
           3. Saat membesar: goyang-goyang (wobble) & transparansi memudar (fade out)
           ========================================================================== */
        /* Once per session rule: hide instantly if already shown */
        html.splash-already-shown #appSplashScreen {
            display: none !important;
        }
        #appSplashScreen {
            position: fixed;
            inset: 0;
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: radial-gradient(circle at 50% 40%, #151d2d 0%, #07090f 90%) !important;
            color: #ffffff;
            user-select: none;
            transition: opacity 0.5s ease-out, visibility 0.5s;
        }

        #appSplashScreen.splash-done {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Screen Shake effect on Impact */
        .splash-stage-root {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .splash-stage-root.shaking {
            animation: cartoonScreenShake 0.4s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }

        @keyframes cartoonScreenShake {
            10%, 90% { transform: translate3d(-5px, 4px, 0); }
            20%, 80% { transform: translate3d(6px, -5px, 0); }
            30%, 50%, 70% { transform: translate3d(-7px, 6px, 0); }
            40%, 60% { transform: translate3d(6px, -4px, 0); }
        }

        /* ── Unified Stage: Karakter & Logo Berbagi Titik Pusat yang Sama ── */
        .unified-stage {
            position: relative;
            width: 320px;
            height: 480px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Ground & Shadows (Tepat di bawah kaki karakter) */
        .ground-area {
            position: absolute;
            top: 265px;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .char-ground-shadow {
            position: absolute;
            width: 84px;
            height: 16px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.55);
            filter: blur(4px);
            animation: charShadowPulse 0.55s infinite alternate ease-in-out;
            transition: opacity 0.3s;
        }
        @keyframes charShadowPulse {
            0% { transform: scale(1); opacity: 0.55; }
            100% { transform: scale(0.85); opacity: 0.35; }
        }

        .falling-logo-shadow {
            position: absolute;
            width: 115px;
            height: 24px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(0, 0, 0, 0.85) 0%, transparent 70%);
            opacity: 0;
            transform: scale(0.2);
            transition: opacity 0.2s, transform 0.2s;
        }
        .falling-logo-shadow.growing {
            animation: logoShadowGrow 0.35s cubic-bezier(0.12, 0, 0.39, 0) forwards;
        }
        @keyframes logoShadowGrow {
            0% { opacity: 0; transform: scale(0.2); }
            100% { opacity: 0.95; transform: scale(1.25); }
        }

        /* Shockwave on Impact */
        .impact-shockwave {
            position: absolute;
            width: 140px;
            height: 32px;
            border-radius: 50%;
            border: 3.5px solid #38bdf8;
            box-shadow: 0 0 18px #38bdf8;
            opacity: 0;
            pointer-events: none;
        }
        .impact-shockwave.active {
            animation: shockwaveExpand 0.55s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
        }
        @keyframes shockwaveExpand {
            0% { transform: scale(0.2); opacity: 1; border-width: 4px; }
            100% { transform: scale(2.8); opacity: 0; border-width: 1px; }
        }

        /* Screen Flash */
        .impact-screen-flash {
            position: fixed;
            inset: 0;
            background: #ffffff;
            opacity: 0;
            pointer-events: none;
            z-index: 10;
        }
        .impact-screen-flash.flash-now {
            animation: flashPop 0.32s ease-out forwards;
        }
        @keyframes flashPop {
            0% { opacity: 0.65; }
            100% { opacity: 0; }
        }

        /* Comic Debris & Dizzy Stars */
        .impact-stars-wrap {
            position: absolute;
            top: 155px;
            left: 50%;
            transform: translateX(-50%);
            width: 160px;
            height: 80px;
            pointer-events: none;
            opacity: 0;
            z-index: 8;
        }
        .impact-stars-wrap.active {
            opacity: 1;
        }
        .comic-star {
            position: absolute;
            font-size: 18px;
            font-weight: 900;
            color: #fbbf24;
            text-shadow: 0 0 10px #f59e0b;
        }
        .impact-stars-wrap.active .star-1 { animation: starFly1 0.75s ease-out forwards; }
        .impact-stars-wrap.active .star-2 { animation: starFly2 0.75s ease-out forwards; }
        .impact-stars-wrap.active .star-3 { animation: starFly3 0.75s ease-out forwards; }
        .impact-stars-wrap.active .star-4 { animation: starFly4 0.75s ease-out forwards; }
        .impact-stars-wrap.active .star-5 { animation: starFly5 0.75s ease-out forwards; }

        @keyframes starFly1 { 0% { transform: scale(0) translate(0, 0); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1.2) translate(-35px, -30px) rotate(-90deg); opacity: 0; } }
        @keyframes starFly2 { 0% { transform: scale(0) translate(0, 0); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1.3) translate(0px, -45px) rotate(120deg); opacity: 0; } }
        @keyframes starFly3 { 0% { transform: scale(0) translate(0, 0); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1.2) translate(35px, -30px) rotate(90deg); opacity: 0; } }
        @keyframes starFly4 { 0% { transform: scale(0) translate(0, 0); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1) translate(-45px, 0px) rotate(-60deg); opacity: 0; } }
        @keyframes starFly5 { 0% { transform: scale(0) translate(0, 0); opacity: 0; } 40% { opacity: 1; } 100% { transform: scale(1) translate(45px, 0px) rotate(60deg); opacity: 0; } }

        /* Floating Thought Particles saat membaca */
        .thought-particles-area {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            width: 170px;
            height: 120px;
            pointer-events: none;
            z-index: 2;
        }
        .thought-particle {
            position: absolute;
            font-size: 14px;
            font-weight: 800;
            color: #38bdf8;
            opacity: 0;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.6);
            animation: thoughtDrift 2.2s infinite ease-out;
        }
        .tp-1 { left: 20%; font-size: 16px; color: #facc15; animation-delay: 0.1s; }
        .tp-2 { left: 45%; font-size: 13px; color: #34d399; animation-delay: 0.6s; }
        .tp-3 { left: 68%; font-size: 15px; color: #a78bfa; animation-delay: 1.1s; }
        .tp-4 { left: 35%; font-size: 14px; color: #fb7185; animation-delay: 1.6s; }

        @keyframes thoughtDrift {
            0% { transform: translateY(100px) scale(0.6); opacity: 0; }
            20% { opacity: 0.9; }
            70% { opacity: 0.9; }
            100% { transform: translateY(10px) scale(1.15) rotate(15deg); opacity: 0; }
        }

        /* ── Karakter Kartun Berjalan (Posisinya sejajar tepat di bawah logo) ── */
        .cartoon-character-wrap {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 135px;
            height: 175px;
            transform-origin: bottom center;
            z-index: 3;
            transition: transform 0.1s cubic-bezier(0.1, 0.9, 0.2, 1), opacity 0.45s ease-out;
        }
        .cartoon-svg {
            width: 100%;
            height: 100%;
            display: block;
            overflow: visible;
        }

        /* Walking Animation Loop */
        .cartoon-character-wrap.walking {
            animation: charBodyWalk 0.55s infinite alternate ease-in-out;
        }
        @keyframes charBodyWalk {
            0% { transform: translateX(-50%) translateY(0px) rotate(0deg); }
            50% { transform: translateX(-50%) translateY(-7px) rotate(1.5deg); }
            100% { transform: translateX(-50%) translateY(0px) rotate(-1.5deg); }
        }
        .cartoon-character-wrap.walking .leg-left {
            transform-origin: 58px 110px;
            animation: walkLegL 0.55s infinite alternate ease-in-out;
        }
        .cartoon-character-wrap.walking .leg-right {
            transform-origin: 82px 110px;
            animation: walkLegR 0.55s infinite alternate ease-in-out;
        }
        @keyframes walkLegL { 0% { transform: rotate(-22deg); } 100% { transform: rotate(20deg); } }
        @keyframes walkLegR { 0% { transform: rotate(20deg); } 100% { transform: rotate(-22deg); } }

        .cartoon-character-wrap.walking .arms-book-group {
            transform-origin: 70px 80px;
            animation: bookBob 0.55s infinite alternate ease-in-out;
        }
        @keyframes bookBob {
            0% { transform: translateY(0px) rotate(0deg); }
            100% { transform: translateY(-3px) rotate(-1deg); }
        }

        /* Dust Puffs */
        .foot-dust {
            position: absolute;
            bottom: 8px;
            width: 10px;
            height: 6px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            pointer-events: none;
        }
        .foot-dust.left { left: 34px; animation: dustPuffL 0.55s infinite ease-out; }
        .foot-dust.right { right: 34px; animation: dustPuffR 0.55s infinite ease-out 0.27s; }
        @keyframes dustPuffL {
            0% { transform: scale(0.5) translate(0, 0); opacity: 0; }
            40% { opacity: 0.7; }
            100% { transform: scale(1.6) translate(-8px, -4px); opacity: 0; }
        }
        @keyframes dustPuffR {
            0% { transform: scale(0.5) translate(0, 0); opacity: 0; }
            40% { opacity: 0.7; }
            100% { transform: scale(1.6) translate(8px, -4px); opacity: 0; }
        }

        /* Alert Exclamation Bubble */
        .char-alert-bubble {
            position: absolute;
            top: -24px;
            left: 50%;
            transform: translateX(-50%) scale(0);
            width: 32px;
            height: 32px;
            background: #ef4444;
            border: 2.5px solid #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 20px;
            font-weight: 900;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
            opacity: 0;
            z-index: 5;
            transition: transform 0.22s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.2s;
        }
        .char-alert-bubble.active {
            opacity: 1;
            transform: translateX(-50%) scale(1.15);
        }

        /* SQUASHED STATE (Gepeng tepat di bawah logo!) */
        .cartoon-character-wrap.squashed {
            animation: none !important;
            /* Gepeng rata di bawah logo */
            transform: translateX(-50%) translateY(92px) scaleX(1.75) scaleY(0.12) !important;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.6));
            transition: transform 0.08s cubic-bezier(0.1, 0.9, 0.2, 1);
        }
        .cartoon-character-wrap.squashed .foot-dust,
        .cartoon-character-wrap.squashed .char-alert-bubble {
            display: none !important;
        }
        .cartoon-character-wrap.dissolve {
            opacity: 0 !important;
            transition: opacity 0.45s ease-out;
        }

        /* ── HERO LOGO (Jatuh tepat menimpa karakter di top: 125px) ── */
        .hero-logo-box {
            position: absolute;
            top: 125px;
            left: 50%;
            width: 104px;
            height: 104px;
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6), 0 0 24px rgba(56, 189, 248, 0.4);
            border: 2px solid rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            transform-origin: center center;
            z-index: 6;
            will-change: transform, opacity;
            /* Awalnya di atas layar */
            transform: translateX(-50%) translateY(-700px);
            opacity: 0;
            transition: none;
        }
        .hero-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 26px;
            display: block;
        }

        .logo-speed-trails {
            position: absolute;
            top: -70px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 80px;
            background: linear-gradient(to top, rgba(56, 189, 248, 0.8), transparent);
            filter: blur(4px);
            opacity: 0;
            pointer-events: none;
        }

        /* Aksi 1: Meluncur Jatuh */
        .hero-logo-box.dropping {
            opacity: 1;
            /* Mendarat tepat menimpa karakter */
            transform: translateX(-50%) translateY(0);
            transition: transform 0.32s cubic-bezier(0.55, 0.055, 0.675, 0.19);
        }
        .hero-logo-box.dropping .logo-speed-trails {
            opacity: 1;
        }

        /* Aksi 2: Membal Benturan (Impact Bounce) */
        .hero-logo-box.impacted {
            opacity: 1;
            animation: heroLogoImpactBounce 0.5s ease-out forwards;
        }
        @keyframes heroLogoImpactBounce {
            0% { transform: translateX(-50%) translateY(0) scale(1.4, 0.65); }
            35% { transform: translateX(-50%) translateY(-22px) scale(0.85, 1.25); }
            60% { transform: translateX(-50%) translateY(0) scale(1.1, 0.95); }
            80% { transform: translateX(-50%) translateY(-4px) scale(0.98, 1.02); }
            100% { transform: translateX(-50%) translateY(0) scale(1, 1); }
        }

        /* ── TAMPILAN BRANDING (SESUAI FOTO 1) ── */
        .hero-text-block {
            position: absolute;
            top: 248px;
            left: 50%;
            transform: translateX(-50%) translateY(14px);
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 300px;
            opacity: 0;
            transition: opacity 0.45s ease-out, transform 0.45s ease-out;
            pointer-events: none;
            z-index: 5;
        }
        .hero-text-block.show-photo1 {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .hero-text-block.hide-photo1 {
            opacity: 0;
            transform: translateX(-50%) translateY(10px);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }

        .hero-brand-title {
            margin: 0;
            font-size: 2.2rem;
            font-weight: 800;
            font-style: italic;
            letter-spacing: -0.025em;
            line-height: 1.1;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }
        .title-white {
            color: #ffffff;
            font-style: italic;
        }
        .title-blue {
            color: #38bdf8;
            font-style: italic;
            text-shadow: 0 0 16px rgba(56, 189, 248, 0.5);
        }

        .hero-blue-accent {
            width: 38px;
            height: 3.5px;
            border-radius: 999px;
            background: #38bdf8;
            box-shadow: 0 0 10px rgba(56, 189, 248, 0.8), 0 0 20px rgba(56, 189, 248, 0.4);
            margin: 10px auto 14px;
        }

        .hero-tagline {
            margin: 0;
            font-size: 0.94rem;
            font-weight: 500;
            color: #94a3b8;
            letter-spacing: -0.01em;
            text-align: center;
        }

        /* ── REVISI: LOGO MEMBESAR SAMBIL GOYANG-GOYANG & TRANSPARANSI MEMUDAR ── */
        .hero-logo-box.wobble-grow-fade {
            animation: logoWobbleGrowFade 1.1s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards !important;
        }

        @keyframes logoWobbleGrowFade {
            0% {
                transform: translateX(-50%) translateY(0) scale(1) rotate(0deg);
                opacity: 1;
                filter: drop-shadow(0 16px 40px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 20px rgba(56, 189, 248, 0.4));
            }
            18% {
                transform: translateX(-50%) translateY(-4px) scale(1.22) rotate(-6deg);
                opacity: 0.95;
                filter: drop-shadow(0 20px 45px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 25px rgba(56, 189, 248, 0.55));
            }
            38% {
                transform: translateX(-50%) translateY(-2px) scale(1.55) rotate(6deg);
                opacity: 0.85;
                filter: drop-shadow(0 24px 50px rgba(0, 0, 0, 0.6)) drop-shadow(0 0 30px rgba(56, 189, 248, 0.6));
            }
            58% {
                transform: translateX(-50%) translateY(0) scale(1.95) rotate(-5deg);
                opacity: 0.65;
            }
            78% {
                transform: translateX(-50%) translateY(0) scale(2.4) rotate(3deg);
                opacity: 0.35;
            }
            100% {
                transform: translateX(-50%) translateY(0) scale(2.85) rotate(0deg);
                opacity: 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            #appSplashScreen { transition-duration: 0.15s !important; }
            .cartoon-character-wrap, .hero-logo-box { animation: none !important; transition: none !important; }
        }
    </style>
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        try {
            if (sessionStorage.getItem('uangkos_splash_shown')) {
                document.documentElement.classList.add('splash-already-shown');
            }
        } catch (e) {}
    </script>
</head>
<body @auth class="{{ implode(' ', array_filter([auth()->user()->dark_mode ? 'dark-mode' : null, auth()->user()->application_mode === 'salary' ? 'salary-mode' : (auth()->user()->application_mode === 'kos' ? 'kos-mode' : null), request()->routeIs('application-mode.choose') ? 'mode-selection' : null])) }}" @endauth>
    <!-- ── Cartoon Walk, Crash & Wobbly Dissolve Splash Screen ── -->
    <div id="appSplashScreen" aria-hidden="true">
        <div class="impact-screen-flash" id="splashFlash"></div>

        <div class="splash-stage-root" id="splashStageRoot">
            <div class="unified-stage">
                <!-- Partikel efek saat berjalan membaca buku -->
                <div class="thought-particles-area" id="thoughtParticles">
                    <span class="thought-particle tp-1">Rp</span>
                    <span class="thought-particle tp-2">♫</span>
                    <span class="thought-particle tp-3">10k</span>
                    <span class="thought-particle tp-4">✦</span>
                </div>

                <!-- Area Tanah & Bayangan (Sejajar tepat di bawah kaki karakter) -->
                <div class="ground-area">
                    <div class="char-ground-shadow" id="charShadow"></div>
                    <div class="falling-logo-shadow" id="fallingLogoShadow"></div>
                    <div class="impact-shockwave" id="splashShockwave"></div>
                </div>

                <!-- Bintang & Koin Berputar Efek Impact -->
                <div class="impact-stars-wrap" id="splashStars">
                    <span class="comic-star star-1">★</span>
                    <span class="comic-star star-2">Rp</span>
                    <span class="comic-star star-3">★</span>
                    <span class="comic-star star-4">✦</span>
                    <span class="comic-star star-5">Rp</span>
                </div>

                <!-- Orang Kartun Berjalan Membawa/Membaca Buku -->
                <div class="cartoon-character-wrap walking" id="cartoonChar">
                    <div class="char-alert-bubble" id="charAlert">!</div>

                    <svg class="cartoon-svg" viewBox="0 0 140 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="38" y="65" width="18" height="34" rx="8" fill="#1e293b" />
                        <rect x="42" y="70" width="10" height="20" rx="4" fill="#334155" />

                        <!-- Kaki Kiri -->
                        <g class="leg-group leg-left">
                            <path d="M58 110 L54 145" stroke="#1e293b" stroke-width="12" stroke-linecap="round"/>
                            <ellipse cx="50" cy="152" rx="13" ry="6" fill="#f8fafc" stroke="#0f172a" stroke-width="2"/>
                            <path d="M42 153 L58 153" stroke="#ef4444" stroke-width="2"/>
                        </g>

                        <!-- Torso Hoodie -->
                        <g class="torso-group">
                            <path d="M48 68 C48 62 92 62 92 68 L88 116 C88 120 52 120 52 116 Z" fill="#0284c7" />
                            <path d="M56 94 C56 90 84 90 84 94 L81 110 L59 110 Z" fill="#0369a1" />
                            <path d="M66 68 L66 82 M74 68 L74 82" stroke="#ffffff" stroke-width="2" stroke-linecap="round"/>
                        </g>

                        <!-- Kaki Kanan -->
                        <g class="leg-group leg-right">
                            <path d="M82 110 L86 145" stroke="#334155" stroke-width="12" stroke-linecap="round"/>
                            <ellipse cx="92" cy="152" rx="13" ry="6" fill="#f8fafc" stroke="#0f172a" stroke-width="2"/>
                            <path d="M84 153 L100 153" stroke="#3b82f6" stroke-width="2"/>
                        </g>

                        <!-- Tangan Membawa Buku Terbuka -->
                        <g class="arms-book-group">
                            <path d="M50 72 C42 85 45 98 56 100" stroke="#0284c7" stroke-width="9" stroke-linecap="round" fill="none"/>
                            <g class="the-book">
                                <path d="M52 82 L70 94 L88 82 L90 106 L70 118 L50 106 Z" fill="#d97706" />
                                <path d="M54 84 L70 95 L70 116 L52 105 Z" fill="#fef9c3" />
                                <path d="M70 95 L86 84 L88 105 L70 116 Z" fill="#ffffff" />
                                <path d="M56 88 L67 96 M56 93 L67 101 M56 98 L67 106" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M73 96 L84 88 M73 101 L84 93 M73 106 L84 98" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
                                <path d="M70 95 L70 124 L66 120 L62 124 L62 112" fill="#ef4444" />
                                <text x="70" y="88" text-anchor="middle" font-size="7" font-weight="900" fill="#78350f" font-family="'Inter', sans-serif">Rp</text>
                            </g>
                            <path d="M90 72 C98 85 95 98 84 100" stroke="#0284c7" stroke-width="9" stroke-linecap="round" fill="none"/>
                            <circle cx="52" cy="100" r="5" fill="#fed7aa"/>
                            <circle cx="88" cy="100" r="5" fill="#fed7aa"/>
                        </g>

                        <!-- Kepala & Wajah -->
                        <g class="head-group">
                            <rect x="66" y="55" width="8" height="12" rx="3" fill="#fdba74"/>
                            <circle cx="70" cy="42" r="22" fill="#fed7aa"/>
                            <circle cx="56" cy="47" r="4" fill="#fca5a5" opacity="0.6"/>
                            <circle cx="84" cy="47" r="4" fill="#fca5a5" opacity="0.6"/>
                            <ellipse cx="61" cy="40" rx="3.5" ry="5" fill="#ffffff"/>
                            <ellipse cx="79" cy="40" rx="3.5" ry="5" fill="#ffffff"/>
                            <ellipse cx="62" cy="42" rx="2.2" ry="3.2" fill="#0f172a"/>
                            <ellipse cx="80" cy="42" rx="2.2" ry="3.2" fill="#0f172a"/>
                            <circle cx="61" cy="41" r="0.9" fill="#ffffff"/>
                            <circle cx="79" cy="41" r="0.9" fill="#ffffff"/>
                            <path d="M57 33 Q62 31 66 33" stroke="#475569" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                            <path d="M74 33 Q78 31 83 33" stroke="#475569" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                            <circle cx="70" cy="44" r="1.5" fill="#f97316"/>
                            <path d="M67 48 Q70 52 73 48" stroke="#0f172a" stroke-width="1.8" stroke-linecap="round" fill="none"/>
                            <path d="M48 40 C46 25 58 18 70 18 C82 18 94 25 92 40 C91 32 87 26 80 25 C75 24 71 28 66 26 C60 24 53 28 48 40 Z" fill="#1e293b"/>
                            <path d="M52 28 C56 22 62 21 68 22" stroke="#334155" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                            <path d="M70 18 Q76 13 82 15" stroke="#1e293b" stroke-width="3" stroke-linecap="round" fill="none"/>
                        </g>
                    </svg>

                    <span class="foot-dust left"></span>
                    <span class="foot-dust right"></span>
                </div>

                <!-- Logo Hero (Jatuh tepat menimpa karakter) -->
                <div class="hero-logo-box" id="heroLogoBox">
                    <div class="logo-speed-trails"></div>
                    <img src="{{ asset('app-logo.png') }}" alt="Logo UangKos">
                </div>

                <!-- Teks Branding (Sesuai Foto 1: UangKos + Tagline) -->
                <div class="hero-text-block" id="heroTextBlock">
                    <h1 class="hero-brand-title">
                        <span class="title-white">Uang</span><span class="title-blue">Kos</span>
                    </h1>
                    <div class="hero-blue-accent"></div>
                    <p class="hero-tagline">Atur uang kos & keuanganmu dengan mudah.</p>
                </div>
            </div>
        </div>
    </div>

    @auth
        <header class="topbar">
            <div class="topbar-leading">
                <div class="menu-wrap">
                    <button class="menu-button" id="menuButton" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="mainMenu">
                        <span></span><span></span><span></span>
                    </button>
                    <nav class="menu-panel" id="mainMenu" aria-label="Menu akun">
                        <a class="{{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">Profil</a>
                        <a class="{{ request()->routeIs('budget.settings') ? 'active' : '' }}" href="{{ route('budget.settings') }}">Pengaturan</a>
                        <div class="menu-logout">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="profile-logout" type="submit">Keluar</button>
                            </form>
                        </div>
                    </nav>
                </div>
                <a class="brand" href="{{ auth()->user()->application_mode === 'salary' ? route('salary.index') : route('budget.index') }}">
                    <img class="brand-icon" src="{{ asset('app-logo.png') }}" alt="Logo UangKos">
                    <span>UangKos</span>
                </a>
            </div>
        </header>
    @endauth
    <main class="page-shell {{ request()->routeIs('login', 'register') ? 'auth-shell' : '' }}">
        @yield('content')
    </main>
    @auth
        <form class="theme-fab" method="POST" action="{{ route('theme.update') }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="dark_mode" value="{{ auth()->user()->dark_mode ? 0 : 1 }}">
            <button type="submit" aria-label="{{ auth()->user()->dark_mode ? 'Ganti ke mode terang' : 'Ganti ke mode gelap' }}">
                <svg class="theme-fab-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    @if (auth()->user()->dark_mode)
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"></path>
                    @else
                        <path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"></path>
                    @endif
                </svg>
            </button>
        </form>
        <script nonce="{{ request()->attributes->get('cspNonce') }}">
            const menuButton = document.getElementById('menuButton');
            const mainMenu = document.getElementById('mainMenu');
            menuButton.addEventListener('click', () => {
                const isOpen = mainMenu.classList.toggle('open');
                menuButton.setAttribute('aria-expanded', String(isOpen));
            });
            document.addEventListener('click', event => {
                if (!event.target.closest('.menu-wrap')) {
                    mainMenu.classList.remove('open');
                    menuButton.setAttribute('aria-expanded', 'false');
                }
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    mainMenu.classList.remove('open');
                    menuButton.setAttribute('aria-expanded', 'false');
                    menuButton.focus();
                }
            });
        </script>
    @endauth
    @auth
        @php
            $salaryMode = auth()->user()->application_mode === 'salary';
            $homeRoute = $salaryMode ? 'salary.index' : 'budget.index';
            $transactionsRoute = $salaryMode ? 'salary.transactions.index' : 'transactions.index';
            $transactionsPattern = $salaryMode ? 'salary.transactions.*' : 'transactions.*';
            $reportsRoute = $salaryMode ? 'salary.reports.index' : 'reports.index';
            $reportsPattern = $salaryMode ? 'salary.reports.*' : 'reports.*';
        @endphp
        <nav class="bottom-nav" aria-label="Navigasi utama">
            <div class="bottom-nav-group">
                <a class="{{ request()->routeIs($transactionsPattern) ? 'active' : '' }}" href="{{ route($transactionsRoute) }}" @if (request()->routeIs($transactionsPattern)) aria-current="page" @endif>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16M4 12h16M4 19h16"></path><circle cx="7" cy="5" r="1"></circle><circle cx="7" cy="12" r="1"></circle><circle cx="7" cy="19" r="1"></circle></svg>
                    <span>Transaksi</span>
                </a>
                <a class="{{ request()->routeIs($reportsPattern) ? 'active' : '' }}" href="{{ route($reportsRoute) }}" @if (request()->routeIs($reportsPattern)) aria-current="page" @endif>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5m0 14h17"></path><path d="m7 15 4-4 3 2 5-7"></path></svg>
                    <span>Laporan</span>
                </a>
            </div>
            <a class="bottom-nav-home {{ request()->routeIs($homeRoute) ? 'active' : '' }}" href="{{ route($homeRoute) }}" @if (request()->routeIs($homeRoute)) aria-current="page" @endif>
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="{{ request()->routeIs($homeRoute) ? 'currentColor' : 'none' }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path></svg>
                <span>Beranda</span>
            </a>
            <div class="bottom-nav-group">
                <a class="{{ request()->routeIs('profile.*', 'budget.settings') ? 'active' : '' }}" href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*', 'budget.settings')) aria-current="page" @endif>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg>
                    <span>Profil</span>
                </a>
            </div>
        </nav>
        <script nonce="{{ request()->attributes->get('cspNonce') }}">
            document.querySelectorAll('.bottom-nav a').forEach(item => {
                item.addEventListener('click', event => {
                    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

                    const bounds = item.getBoundingClientRect();
                    const ripple = document.createElement('span');
                    ripple.className = 'bottom-nav-ripple';
                    ripple.style.left = `${event.clientX ? event.clientX - bounds.left : bounds.width / 2}px`;
                    ripple.style.top = `${event.clientY ? event.clientY - bounds.top : bounds.height / 2}px`;
                    item.appendChild(ripple);
                    ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
                });
            });
        </script>
    @endauth
    @stack('scripts')
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        document.querySelectorAll('[data-password-toggle]').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const input = document.getElementById(toggle.getAttribute('aria-controls'));
                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', String(!isVisible));
                toggle.setAttribute('aria-label', isVisible ? 'Tampilkan password' : 'Sembunyikan password');
            });
        });

        document.querySelectorAll('[data-currency-input]').forEach(input => {
            input.addEventListener('input', () => {
                const digitsBeforeCaret = input.value.slice(0, input.selectionStart ?? input.value.length).replace(/\D/g, '').length;
                const digits = input.value.replace(/\D/g, '');
                const formatted = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                input.value = formatted;

                let caret = 0;
                let digitCount = 0;
                while (caret < formatted.length && digitCount < digitsBeforeCaret) {
                    if (/\d/.test(formatted[caret])) digitCount++;
                    caret++;
                }
                input.setSelectionRange(caret, caret);
            });
        });
    </script>
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        (function() {
            var splash = document.getElementById('appSplashScreen');
            if (!splash) return;

            // Check if splash has already been shown in this session (Hanya 1x saat aplikasi dibuka)
            try {
                if (sessionStorage.getItem('uangkos_splash_shown')) {
                    splash.remove();
                    return;
                }
                sessionStorage.setItem('uangkos_splash_shown', '1');
            } catch (e) {}

            var stageRoot = document.getElementById('splashStageRoot');
            var cartoon = document.getElementById('cartoonChar');
            var alertBubble = document.getElementById('charAlert');
            var thoughtParticles = document.getElementById('thoughtParticles');
            var charShadow = document.getElementById('charShadow');
            var fallingLogoShadow = document.getElementById('fallingLogoShadow');
            var shockwave = document.getElementById('splashShockwave');
            var stars = document.getElementById('splashStars');
            var flash = document.getElementById('splashFlash');

            var heroLogo = document.getElementById('heroLogoBox');
            var heroText = document.getElementById('heroTextBlock');

            // ── REVISED SEQUENCE & COMFORTABLE TIMING ──
            // 0.0s - 2.8s: Karakter kartun berjalan santai membaca buku (durasi jelas & nyaman dinikmati)

            // 2.8s: Tanda seru kaget '!' muncul & bayangan logo di tanah membesar
            setTimeout(function() {
                if (alertBubble) alertBubble.classList.add('active');
                if (fallingLogoShadow) fallingLogoShadow.classList.add('growing');
            }, 2800);

            // 3.1s: Logo meluncur jatuh dari atas
            setTimeout(function() {
                if (heroLogo) heroLogo.classList.add('dropping');
            }, 3100);

            // 3.35s: IMPACT / GEDEBUK! Logo menimpa karakter tepat di bawahnya
            setTimeout(function() {
                // Karakter gepeng tepat di bawah logo!
                if (cartoon) {
                    cartoon.classList.remove('walking');
                    cartoon.classList.add('squashed');
                }
                if (stageRoot) stageRoot.classList.add('shaking');
                if (heroLogo) heroLogo.classList.add('impacted');
                if (flash) flash.classList.add('flash-now');
                if (shockwave) shockwave.classList.add('active');
                if (stars) stars.classList.add('active');
                if (thoughtParticles) thoughtParticles.style.opacity = '0';
            }, 3350);

            // 3.75s: TAHAP 1 (FOTO 1)
            // Karakter gepeng larut/menghilang, logo di tengah, teks UangKos & tagline muncul
            setTimeout(function() {
                if (cartoon) cartoon.classList.add('dissolve');
                if (charShadow) charShadow.style.opacity = '0';
                if (heroText) heroText.classList.add('show-photo1');
            }, 3750);

            // 5.4s: TAHAP 2 (REVISI USER)
            // Teks memudar, logo MEMBESAR SAMBIL GOYANG-GOYANG & TRANSPARAN BERKURANG (fade out)
            setTimeout(function() {
                if (heroText) {
                    heroText.classList.remove('show-photo1');
                    heroText.classList.add('hide-photo1');
                }
                if (heroLogo) {
                    heroLogo.classList.remove('impacted');
                    heroLogo.classList.add('wobble-grow-fade');
                }
            }, 5400);

            // 6.35s: Selesai, splash overlay memudar lembut keluar
            setTimeout(function() {
                splash.classList.add('splash-done');
                splash.addEventListener('transitionend', function() {
                    splash.remove();
                }, { once: true });
            }, 6350);

            // Fallback safety (7.5s)
            setTimeout(function() {
                if (splash && !splash.classList.contains('splash-done')) {
                    splash.classList.add('splash-done');
                    setTimeout(function() { splash.remove(); }, 300);
                }
            }, 7500);
        })();
    </script>




</body>
</html>
