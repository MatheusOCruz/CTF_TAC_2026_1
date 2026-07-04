<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMon Corp — Infrastructure Monitoring</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f1117; color: #c9d1d9; }
        header { background: #161b22; border-bottom: 1px solid #30363d; padding: 14px 40px; display: flex; align-items: center; gap: 16px; }
        header .logo { font-size: 1.2rem; font-weight: 700; color: #58a6ff; letter-spacing: 1px; }
        header nav a { color: #8b949e; text-decoration: none; margin-left: 24px; font-size: 0.9rem; }
        header nav a:hover { color: #c9d1d9; }
        .hero { padding: 80px 40px 60px; max-width: 900px; margin: 0 auto; }
        .hero h1 { font-size: 2.4rem; font-weight: 700; color: #e6edf3; margin-bottom: 16px; }
        .hero p { font-size: 1.1rem; color: #8b949e; line-height: 1.7; max-width: 600px; }
        .status-bar { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 20px 28px; max-width: 900px; margin: 0 auto 40px; display: flex; gap: 40px; }
        .status-item .label { font-size: 0.75rem; color: #8b949e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .status-item .value { font-size: 1.1rem; color: #e6edf3; font-weight: 600; }
        .dot-green { display: inline-block; width: 8px; height: 8px; background: #3fb950; border-radius: 50%; margin-right: 6px; }
        .cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; max-width: 900px; margin: 0 auto; padding: 0 40px 60px; }
        .card { background: #161b22; border: 1px solid #30363d; border-radius: 8px; padding: 24px; }
        .card h3 { font-size: 0.95rem; color: #58a6ff; margin-bottom: 10px; }
        .card p { font-size: 0.85rem; color: #8b949e; line-height: 1.6; }
        footer { border-top: 1px solid #30363d; padding: 20px 40px; text-align: center; font-size: 0.8rem; color: #484f58; }
    </style>
</head>
<body>
    <header>
        <span class="logo">⬡ SysMon Corp</span>
        <nav>
            <a href="#">Dashboard</a>
            <a href="#">Hosts</a>
            <a href="#">Alerts</a>
            <a href="#">Reports</a>
        </nav>
    </header>

    @if(session('error'))
    <div style="position:fixed;top:60px;right:40px;background:#ff4444;color:white;padding:10px 18px;border-radius:8px;font-size:0.85rem;z-index:999">
        {{ session('error') }}
    </div>
    @endif

    <div style="position:fixed;top:14px;right:40px">
        <form action="/login" method="POST" style="display:flex;gap:8px;align-items:center">
            @csrf
            <input type="email" name="email" placeholder="Email" style="padding:6px 10px;border-radius:6px;border:1px solid #30363d;background:#0f1117;color:#c9d1d9;font-size:0.85rem">
            <input type="password" name="password" placeholder="Password" style="padding:6px 10px;border-radius:6px;border:1px solid #30363d;background:#0f1117;color:#c9d1d9;font-size:0.85rem">
            <button type="submit" style="padding:6px 14px;background:#58a6ff;color:#0f1117;border:none;border-radius:6px;cursor:pointer;font-size:0.85rem">Sign in</button>
        </form>
    </div>

    <div class="hero">
        <h1>Infrastructure Monitoring Platform</h1>
        <p>Real-time visibility into your servers, services, and network devices. Built for operations teams that need reliability.</p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>Server Health</h3>
            <p>Monitor CPU, memory, and disk usage across all registered hosts in real time.</p>
        </div>
        <div class="card">
            <h3>Service Checks</h3>
            <p>Automated checks for HTTP, SSH, FTP, and custom TCP services every 60 seconds.</p>
        </div>
        <div class="card">
            <h3>Alert Engine</h3>
            <p>Threshold-based alerting with email and webhook integrations for your on-call team.</p>
        </div>
    </div>

    <div class="status-bar">
        <div class="status-item">
            <div class="label">System Status</div>
            <div class="value"><span class="dot-green"></span>Operational</div>
        </div>
        <div class="status-item">
            <div class="label">Hosts Monitored</div>
            <div class="value">47</div>
        </div>
        <div class="status-item">
            <div class="label">Active Alerts</div>
            <div class="value">2</div>
        </div>
        <div class="status-item">
            <div class="label">Uptime (30d)</div>
            <div class="value">99.4%</div>
        </div>
    </div>

    <footer>SysMon Corp &copy; 2024 — Internal Use Only — v2.3.1</footer>
</body>
</html>
