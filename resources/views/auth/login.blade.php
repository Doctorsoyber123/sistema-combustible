<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FuelControl &mdash; Iniciar sesion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <style>
        :root {
            --bg: #F4F2EC; --bg2: #FFFFFF; --bg3: #EFEDE4;
            --border: rgba(40,35,25,0.10); --border2: rgba(40,35,25,0.17);
            --text: #2C2A24; --text2: #6B6557; --text3: #9B9484;
            --accent: #E8590C; --accent2: #F97316; --red: #DC2626; --green: #16A34A;
            --radius: 10px; --radius-lg: 14px;
            --shadow: 0 8px 30px rgba(40,35,25,0.10), 0 1px 2px rgba(40,35,25,0.05);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .login-card {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); box-shadow: var(--shadow);
            width: 100%; max-width: 380px; overflow: hidden;
        }
        .login-head { padding: 28px 28px 8px; text-align: center; }
        .brand-icon {
            width: 46px; height: 46px; background: var(--accent); border-radius: 12px;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 14px;
        }
        .brand-icon i { font-size: 24px; color: #fff; }
        .login-title { font-size: 19px; font-weight: 600; }
        .login-sub { font-size: 13px; color: var(--text3); margin-top: 3px; }
        .login-body { padding: 20px 28px 28px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 14px; }
        label { font-size: 12px; color: var(--text2); font-weight: 500; }
        input[type=text], input[type=password] {
            background: var(--bg2); border: 1px solid var(--border2); border-radius: var(--radius);
            color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 14px;
            padding: 10px 12px; outline: none; transition: border-color 0.15s; width: 100%;
        }
        input:focus { border-color: var(--accent); }
        .remember { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: var(--text2); margin-bottom: 16px; }
        .remember input { width: 15px; height: 15px; accent-color: var(--accent); }
        .btn-login {
            width: 100%; background: var(--accent); border: none; color: #fff;
            padding: 11px; border-radius: var(--radius); font-size: 14px; font-weight: 500;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: background 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 7px;
        }
        .btn-login:hover { background: var(--accent2); }
        .alert {
            padding: 10px 12px; border-radius: var(--radius); font-size: 12.5px;
            margin-bottom: 16px; display: flex; align-items: center; gap: 7px;
        }
        .alert-error { background: rgba(220,38,38,0.09); border: 1px solid rgba(220,38,38,0.25); color: #B91C1C; }
        .alert-success { background: rgba(22,163,74,0.10); border: 1px solid rgba(22,163,74,0.25); color: #15803D; }
        .field-error { font-size: 11px; color: var(--red); }
        .login-foot {
            text-align: center; font-size: 11.5px; color: var(--text3);
            padding: 14px; border-top: 1px solid var(--border); background: var(--bg3);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-head">
            <div class="brand-icon"><i class="ti ti-flame"></i></div>
            <div class="login-title">FuelControl</div>
            <div class="login-sub">Gestion de combustible</div>
        </div>
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-error"><i class="ti ti-alert-triangle"></i> {{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
            @endif

            <form action="{{ route('login.attempt') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Ej: admin" required autofocus autocomplete="username">
                    @error('username') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                    @error('password') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <label class="remember">
                    <input type="checkbox" name="remember" value="1"> Mantener sesion iniciada
                </label>
                <button type="submit" class="btn-login"><i class="ti ti-login-2"></i> Iniciar sesion</button>
            </form>
        </div>
        <div class="login-foot">Acceso restringido al personal autorizado</div>
    </div>
</body>
</html>
