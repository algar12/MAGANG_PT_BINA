<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Timbangan IoT — Sistem Manajemen Produksi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --accent: #0ea5e9;
            --dark: #0f172a;
            --dark2: #1e293b;
            --dark3: #334155;
            --text: #e2e8f0;
            --text-muted: #94a3b8;
            --border: rgba(255,255,255,0.08);
            --glass: rgba(255,255,255,0.05);
        }

        html, body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── Animated Background ── */
        .bg-scene {
            position: fixed; inset: 0; z-index: 0;
            background: radial-gradient(ellipse at 20% 50%, #1e3a5f 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, #0c2a4a 0%, transparent 50%),
                        radial-gradient(ellipse at 60% 80%, #162032 0%, transparent 50%),
                        #0f172a;
        }
        .bg-scene::after {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.04) 1px, transparent 0);
            background-size: 40px 40px;
        }
        .orb {
            position: absolute; border-radius: 50%;
            filter: blur(80px); opacity: 0.25; animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 400px; height: 400px; background: #2563eb; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: #0ea5e9; bottom: -50px; right: -50px; animation-delay: 3s; }
        .orb-3 { width: 200px; height: 200px; background: #7c3aed; top: 50%; left: 50%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* ── Layout ── */
        .page {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 24px;
        }

        /* ── Brand ── */
        .brand {
            text-align: center; margin-bottom: 48px;
            animation: fadeDown 0.7s ease both;
        }
        .brand-icon {
            display: inline-flex; align-items: center; justify-content: center;
            width: 72px; height: 72px; border-radius: 20px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            margin-bottom: 20px;
            box-shadow: 0 0 40px rgba(37,99,235,0.4);
        }
        .brand-icon svg { width: 36px; height: 36px; }
        .brand h1 {
            font-size: 2rem; font-weight: 800; letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 40%, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .brand p {
            margin-top: 8px; font-size: 0.9rem; color: var(--text-muted); font-weight: 400;
        }

        /* ── Card Grid ── */
        .card-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 24px; width: 100%; max-width: 760px;
        }
        @media (max-width: 600px) { .card-grid { grid-template-columns: 1fr; } }

        /* ── Login Card ── */
        .login-card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
            backdrop-filter: blur(20px);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-card.admin { animation: fadeUp 0.7s ease 0.1s both; }
        .login-card.operator { animation: fadeUp 0.7s ease 0.2s both; }

        .login-card.admin:hover { border-color: rgba(37,99,235,0.5); box-shadow: 0 20px 60px rgba(37,99,235,0.15); }
        .login-card.operator:hover { border-color: rgba(14,165,233,0.5); box-shadow: 0 20px 60px rgba(14,165,233,0.15); }

        .card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
        .card-badge {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .card-badge.blue { background: rgba(37,99,235,0.2); }
        .card-badge.cyan { background: rgba(14,165,233,0.2); }
        .card-badge svg { width: 22px; height: 22px; }

        .card-title { font-size: 1.1rem; font-weight: 700; }
        .card-subtitle { font-size: 0.78rem; color: var(--text-muted); margin-top: 2px; }

        /* ── Divider ── */
        .divider {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
            font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ── Form ── */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 0.78rem; font-weight: 500; color: var(--text-muted); margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            font-size: 0.88rem;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input::placeholder { color: #475569; }
        .form-input:focus {
            border-color: rgba(37,99,235,0.6);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.15);
        }
        .operator .form-input:focus {
            border-color: rgba(14,165,233,0.6);
            box-shadow: 0 0 0 3px rgba(14,165,233,0.15);
        }

        /* ── Buttons ── */
        .btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 11px 20px;
            border: none; border-radius: 10px;
            font-size: 0.88rem; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.2s ease; text-decoration: none;
            margin-top: 4px;
        }
        .btn-admin {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            box-shadow: 0 4px 15px rgba(37,99,235,0.35);
        }
        .btn-admin:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(37,99,235,0.5); }
        .btn-admin:active { transform: translateY(0); }

        .btn-operator {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #fff;
            box-shadow: 0 4px 15px rgba(14,165,233,0.35);
        }
        .btn-operator:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(14,165,233,0.5); }
        .btn-operator:active { transform: translateY(0); }

        .btn svg { width: 16px; height: 16px; }

        /* ── Access Info ── */
        .access-list { list-style: none; margin-bottom: 20px; }
        .access-list li {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.78rem; color: var(--text-muted);
            padding: 5px 0;
            border-bottom: 1px solid var(--border);
        }
        .access-list li:last-child { border-bottom: none; }
        .access-list .dot {
            width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
        }
        .dot-blue { background: #2563eb; }
        .dot-cyan { background: #0ea5e9; }

        /* ── Error Alert ── */
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.25);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #fca5a5;
            margin-bottom: 14px;
            display: flex; align-items: center; gap: 8px;
        }

        /* ── Footer ── */
        .footer {
            margin-top: 40px; text-align: center;
            font-size: 0.75rem; color: var(--text-muted);
            animation: fadeUp 0.7s ease 0.4s both;
        }
        .footer span { color: #475569; }

        /* ── Status Badges ── */
        .status-row {
            display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;
        }
        .status-badge {
            display: flex; align-items: center; gap: 5px;
            font-size: 0.7rem; color: var(--text-muted);
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px 10px;
        }
        .status-dot { width: 5px; height: 5px; border-radius: 50%; background: #22c55e; box-shadow: 0 0 6px #22c55e; }

        /* ── Animations ── */
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="bg-scene">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="page">
    <!-- Brand -->
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8">
                <path d="M3 9l1-5h16l1 5M3 9h18M3 9l2 12h14l2-12" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="15" r="2" fill="white" stroke="none"/>
                <path d="M12 13v-3" stroke-linecap="round"/>
            </svg>
        </div>
        <h1>Smart Timbangan IoT</h1>
        <p>Sistem Manajemen Produksi & Bill of Materials</p>
        <div class="status-row" style="justify-content:center; margin-top:12px;">
            <span class="status-badge"><span class="status-dot"></span>Server Online</span>
            <span class="status-badge"><span class="status-dot"></span>IoT Connected</span>
            <span class="status-badge"><span class="status-dot"></span>Laravel 11</span>
        </div>
    </div>

    <!-- Login Cards -->
    <div class="card-grid">

        <!-- Admin Card -->
        <div class="login-card admin">
            <div class="card-header">
                <div class="card-badge blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Administrator</div>
                    <div class="card-subtitle">Akses Penuh Sistem</div>
                </div>
            </div>

            <ul class="access-list">
                <li><span class="dot dot-blue"></span>Kelola Master Data & Formula</li>
                <li><span class="dot dot-blue"></span>Buat Production Order</li>
                <li><span class="dot dot-blue"></span>Monitor Costing & Laporan</li>
                <li><span class="dot dot-blue"></span>Kelola User & Device</li>
            </ul>

            <div class="divider">Login Admin</div>

            <a href="/admin" id="btn-admin" class="btn btn-admin">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M14 12H3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Masuk ke Admin Panel
            </a>
        </div>

        <!-- Operator Card -->
        <div class="login-card operator">
            <div class="card-header">
                <div class="card-badge cyan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <div class="card-title">Operator Timbangan</div>
                    <div class="card-subtitle">Akses Area Penimbangan</div>
                </div>
            </div>

            <ul class="access-list">
                <li><span class="dot dot-cyan"></span>Monitor Berat Timbangan Live</li>
                <li><span class="dot dot-cyan"></span>Lihat Daftar BOM & Target Netto</li>
                <li><span class="dot dot-cyan"></span>Cek Status Penimbangan per Bahan</li>
                <li><span class="dot dot-cyan"></span>Lihat Sub Cost Aktual Produksi</li>
            </ul>

            <div class="divider">Login Operator Timbangan</div>

            @if ($errors->any())
                <div class="alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('operator.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" type="email" name="email" class="form-input"
                           placeholder="operator@example.com"
                           value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-input"
                           placeholder="••••••••" required autocomplete="current-password">
                </div>
                <button type="submit" id="btn-operator" class="btn btn-operator">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M14 12H3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Masuk sebagai Operator Timbangan
                </button>
            </form>
        </div>

    </div>

    <!-- Footer -->
    <div class="footer">
        <span>Smart Timbangan IoT</span> &mdash; PT Bina Industrial &copy; {{ date('Y') }}
        &nbsp;·&nbsp; Laravel v{{ Illuminate\Foundation\Application::VERSION }}
    </div>
</div>

</body>
</html>
