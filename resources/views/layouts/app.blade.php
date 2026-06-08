<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FuelControl &mdash; @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <style>
        :root {
            --bg: #F4F2EC;
            --bg2: #FFFFFF;
            --bg3: #EFEDE4;
            --border: rgba(40,35,25,0.10);
            --border2: rgba(40,35,25,0.17);
            --text: #2C2A24;
            --text2: #6B6557;
            --text3: #9B9484;
            --accent: #E8590C;
            --accent2: #F97316;
            --blue: #2563EB;
            --green: #16A34A;
            --amber: #D97706;
            --red: #DC2626;
            --radius: 10px;
            --radius-lg: 14px;
            --shadow: 0 1px 3px rgba(40,35,25,0.07), 0 1px 2px rgba(40,35,25,0.04);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; overflow: hidden; height: 100vh;
        }
        a { text-decoration: none; color: inherit; }

        /* SIDEBAR */
        .sidebar {
            width: 234px; min-width: 234px; background: var(--bg2);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column; height: 100vh; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px 18px 16px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .brand-icon {
            width: 32px; height: 32px; background: var(--accent);
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
        }
        .brand-icon i { font-size: 17px; color: #fff; }
        .brand-name { font-size: 15px; font-weight: 600; color: var(--text); }
        .brand-sub { font-size: 11px; color: var(--text3); margin-top: 1px; }
        .nav-section { padding: 14px 10px 6px; }
        .nav-section-label {
            font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;
            color: var(--text3); padding: 0 8px; margin-bottom: 6px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 9px;
            padding: 9px 10px; border-radius: var(--radius);
            cursor: pointer; color: var(--text2); font-size: 13.5px;
            transition: all 0.15s; margin-bottom: 2px; border: 1px solid transparent;
        }
        .nav-item i { font-size: 17px; min-width: 20px; }
        .nav-item:hover { color: var(--text); background: var(--bg); }
        .nav-item.active {
            color: var(--accent); background: rgba(232,89,12,0.10);
            border-color: rgba(232,89,12,0.22); font-weight: 500;
        }

        /* SIDEBAR FOOTER (usuario) */
        .sidebar-footer { margin-top: auto; padding: 12px 12px 14px; border-top: 1px solid var(--border); }
        .user-box { display: flex; align-items: center; gap: 9px; padding: 6px 4px 10px; }
        .user-avatar {
            width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
            background: rgba(232,89,12,0.12); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: 14px;
        }
        .user-name { font-size: 13px; font-weight: 500; color: var(--text); line-height: 1.2; }
        .user-role { font-size: 11px; color: var(--text3); }
        .logout-btn {
            width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 8px 10px; border-radius: var(--radius); cursor: pointer;
            background: var(--bg); border: 1px solid var(--border2);
            color: var(--text2); font-size: 12.5px; font-family: 'DM Sans', sans-serif;
            transition: all 0.15s;
        }
        .logout-btn:hover { color: var(--red); border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }

        /* MAIN */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar {
            padding: 0 24px; height: 58px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: var(--bg2); flex-shrink: 0;
        }
        .topbar-left { display: flex; align-items: center; gap: 10px; }
        .topbar-title { font-size: 15px; font-weight: 600; color: var(--text); }
        .topbar-badge {
            font-size: 11px; padding: 3px 9px; border-radius: 99px;
            background: rgba(232,89,12,0.12); color: var(--accent);
            border: 1px solid rgba(232,89,12,0.22);
        }
        .topbar-date { font-size: 12px; color: var(--text3); font-family: 'DM Mono', monospace; }
        .content { flex: 1; overflow-y: auto; padding: 24px; }

        /* BOTON HAMBURGUESA (solo movil) */
        .menu-toggle {
            display: none; background: none; border: 1px solid var(--border2);
            color: var(--text2); width: 36px; height: 36px; border-radius: 8px;
            align-items: center; justify-content: center; cursor: pointer; padding: 0;
        }
        .menu-toggle i { font-size: 20px; }
        .menu-toggle:hover { background: var(--bg3); color: var(--text); }

        /* OVERLAY DETRAS DEL SIDEBAR EN MOVIL */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(40,35,25,0.45); z-index: 240;
        }
        .sidebar-overlay.open { display: block; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 22px; }
        .stat-card {
            background: var(--bg2); border-radius: var(--radius-lg);
            padding: 16px 18px; border: 1px solid var(--border); box-shadow: var(--shadow);
        }
        .stat-icon {
            width: 34px; height: 34px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 12px;
        }
        .stat-icon i { font-size: 18px; }
        .stat-label { font-size: 12px; color: var(--text2); margin-bottom: 4px; }
        .stat-val { font-size: 24px; font-weight: 600; color: var(--text); line-height: 1; }
        .stat-sub { font-size: 11px; color: var(--text3); margin-top: 4px; }

        /* CARDS */
        .card {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); margin-bottom: 18px; overflow: hidden;
            box-shadow: var(--shadow);
        }
        .card-header {
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
        }
        .card-title {
            font-size: 13.5px; font-weight: 500; color: var(--text);
            display: flex; align-items: center; gap: 8px;
        }
        .card-title i { font-size: 16px; color: var(--accent); }
        .card-body { padding: 18px; }

        /* TABLE */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            text-align: left; color: var(--text3); font-weight: 500;
            font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em;
            padding: 0 12px 10px; border-bottom: 1px solid var(--border);
        }
        tbody td { padding: 11px 12px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(40,35,25,0.025); }
        .mono { font-family: 'DM Mono', monospace; }

        /* CHIPS */
        .chip { display: inline-flex; align-items: center; gap: 4px; font-size: 11px; padding: 3px 9px; border-radius: 99px; font-weight: 500; }
        .chip-orange { background: rgba(232,89,12,0.10); color: #C2410C; border: 1px solid rgba(232,89,12,0.22); }
        .chip-blue   { background: rgba(37,99,235,0.10); color: #1D4ED8; border: 1px solid rgba(37,99,235,0.22); }
        .chip-green  { background: rgba(22,163,74,0.11); color: #15803D; border: 1px solid rgba(22,163,74,0.22); }
        .chip-amber  { background: rgba(217,119,6,0.12); color: #B45309; border: 1px solid rgba(217,119,6,0.22); }
        .chip-red    { background: rgba(220,38,38,0.10); color: #B91C1C; border: 1px solid rgba(220,38,38,0.22); }
        .chip-gray   { background: rgba(40,35,25,0.06); color: var(--text2); border: 1px solid rgba(40,35,25,0.13); }

        /* BUTTONS */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: var(--radius); font-size: 13px;
            font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.15s;
            border: 1px solid var(--border2); background: var(--bg2); color: var(--text); font-weight: 500;
        }
        .btn:hover { background: var(--bg3); }
        .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent2); border-color: var(--accent2); }
        .btn-sm { padding: 6px 10px; font-size: 12px; }
        .btn-danger { color: var(--red); border-color: rgba(220,38,38,0.28); background: var(--bg2); }
        .btn-danger:hover { background: rgba(220,38,38,0.07); }

        /* FORMS */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        /* BOLETAS: filas responsivas para inputs de boleta (número, proveedor, galones, precio, fecha) */
        .boleta-row { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .boleta-row input[type="text"], .boleta-row input[type="number"], .boleta-row input[type="date"] { flex: 1 1 140px; min-width:120px; }
        .boleta-row button { flex: 0 0 auto; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 12px; color: var(--text2); font-weight: 500; }
        input, select, textarea {
            background: var(--bg2); border: 1px solid var(--border2); border-radius: var(--radius);
            color: var(--text); font-family: 'DM Sans', sans-serif; font-size: 13.5px;
            padding: 9px 12px; outline: none; transition: border-color 0.15s; width: 100%;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent); }
        input[readonly] { background: var(--bg3); color: var(--text2); }
        select option { background: var(--bg2); }
        .form-actions {
            display: flex; gap: 10px; justify-content: flex-end;
            margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--border);
        }
        .field-error { font-size: 11px; color: var(--red); }

        /* FILTER BAR */
        .filter-bar {
            display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 14px 16px; margin-bottom: 18px;
            box-shadow: var(--shadow);
        }
        .filter-bar .filter-field { display: flex; flex-direction: column; gap: 5px; }
        .filter-bar label { font-size: 11px; }
        .filter-bar input, .filter-bar select { padding: 7px 10px; font-size: 12.5px; min-width: 150px; }
        .filter-bar .filter-actions { display: flex; gap: 8px; margin-left: auto; }
        .filter-tag {
            font-size: 11px; color: var(--text3);
            display: inline-flex; align-items: center; gap: 5px;
        }

        /* BAR */
        .bar-row { display: flex; align-items: center; gap: 10px; }
        .bar-bg { flex: 1; height: 6px; background: var(--bg3); border-radius: 99px; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 99px; transition: width 0.4s; }
        .bar-label { font-size: 11px; color: var(--text2); min-width: 35px; text-align: right; font-family: 'DM Mono', monospace; }

        /* EMPTY */
        .empty { text-align: center; padding: 40px 20px; color: var(--text3); }
        .empty i { font-size: 36px; margin-bottom: 10px; display: block; }

        /* ALERT */
        .alert { padding: 10px 14px; border-radius: var(--radius); font-size: 13px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-success { background: rgba(22,163,74,0.10); border: 1px solid rgba(22,163,74,0.25); color: #15803D; }
        .alert-error { background: rgba(220,38,38,0.09); border: 1px solid rgba(220,38,38,0.25); color: #B91C1C; }

        /* LAYOUT HELPERS */
        .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .page-actions { display: flex; justify-content: flex-end; margin-bottom: 16px; }

        /* PAGINACION */
        .pagination-wrap {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 10px; padding: 13px 18px; border-top: 1px solid var(--border);
        }
        .pagination-info { font-size: 12px; color: var(--text3); }
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; }
        .pagination .page-link {
            min-width: 32px; height: 32px; padding: 0 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12.5px; color: var(--text2); border: 1px solid var(--border2);
            background: var(--bg2); border-radius: 8px; transition: all 0.15s;
            font-family: 'DM Mono', monospace;
        }
        .pagination .page-link i { font-size: 15px; }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background: var(--bg3); color: var(--text); border-color: var(--border2);
        }
        .pagination .page-item.active .page-link {
            background: var(--accent); border-color: var(--accent); color: #fff; cursor: default;
        }
        .pagination .page-item.disabled .page-link { opacity: 0.4; cursor: default; }
        .pagination .page-dots {
            min-width: 22px; text-align: center; color: var(--text3);
            font-size: 12.5px; font-family: 'DM Mono', monospace;
        }

        /* MODALES */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(40,35,25,0.45);
            display: none; align-items: flex-start; justify-content: center;
            z-index: 200; padding: 42px 20px; overflow-y: auto;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: var(--bg2); border: 1px solid var(--border);
            border-radius: var(--radius-lg); box-shadow: 0 20px 60px rgba(40,35,25,0.28);
            width: 100%; max-width: 720px; overflow: hidden;
            animation: modalIn 0.16s ease;
        }
        .modal-box.modal-sm { max-width: 420px; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        .modal-head {
            padding: 14px 18px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; gap: 10px;
        }
        .modal-title { font-size: 14px; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 8px; }
        .modal-title i { font-size: 17px; color: var(--accent); }
        .modal-close {
            background: none; border: none; cursor: pointer; color: var(--text3);
            font-size: 18px; line-height: 1; padding: 4px; border-radius: 6px; transition: all 0.15s;
        }
        .modal-close:hover { color: var(--red); background: rgba(220,38,38,0.08); }
        .modal-body { padding: 18px; }
        .modal-confirm-body { text-align: center; padding: 26px 22px 6px; }
        .modal-confirm-icon {
            width: 54px; height: 54px; border-radius: 50%; margin: 0 auto 14px;
            background: rgba(232,89,12,0.12); color: var(--accent);
            display: flex; align-items: center; justify-content: center;
        }
        .modal-confirm-icon i { font-size: 28px; }
        .modal-confirm-text { font-size: 14.5px; color: var(--text); font-weight: 500; }
        .modal-confirm-sub { font-size: 12px; color: var(--text3); margin-top: 4px; }
        .modal-foot {
            padding: 14px 18px; border-top: 1px solid var(--border);
            display: flex; gap: 10px; justify-content: flex-end;
        }
        .modal-foot.centered { justify-content: center; }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 99px; }

        @media (max-width: 860px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .row2, .form-grid { grid-template-columns: 1fr; }

            /* En pantallas chicas, asegurar que las filas de boletas ocupen 100% */
            .boleta-row input[type="text"], .boleta-row input[type="number"], .boleta-row input[type="date"] { flex: 1 1 100%; min-width: 0; }

            /* Sidebar fuera de pantalla por defecto, se desliza al abrir */
            .sidebar {
                position: fixed; top: 0; left: 0; z-index: 250;
                width: 260px; min-width: 260px; max-width: 85vw;
                transform: translateX(-100%);
                transition: transform 0.22s ease;
                box-shadow: 6px 0 24px rgba(40,35,25,0.18);
            }
            .sidebar.open { transform: translateX(0); }

            /* El contenido principal ocupa todo el ancho */
            .main { width: 100%; min-width: 0; }
            .topbar { padding: 0 14px; }
            .content { padding: 14px; }

            /* Mostrar el boton hamburguesa en la topbar */
            .menu-toggle { display: inline-flex; }

            /* Ajustes finos para no saturar el header en pantallas chicas */
            .topbar-title { font-size: 14px; }
            .topbar-badge { display: none; }
            .topbar-date { display: none; }

            /* Tablas con scroll horizontal en pantallas chicas */
            .card > div[style*="padding:0 6px 6px"] { overflow-x: auto; -webkit-overflow-scrolling: touch; }

            /* La columna de acciones (ultima) queda fija a la derecha al scrollear */
            table thead th:last-child,
            table tbody td:last-child {
                position: sticky; right: 0;
                background: var(--bg2);
                box-shadow: -8px 0 12px -8px rgba(40,35,25,0.12);
            }

            /* Botones mas grandes para tocar comodamente */
            .btn { min-height: 40px; }
            .btn-sm { min-height: 36px; padding: 8px 12px; }
            .btn-sm i { font-size: 16px; }

            /* Evitar el zoom automatico de iOS al enfocar inputs (font >= 16px) */
            input, select, textarea { font-size: 16px; }

            /* Modales mas comodos en movil */
            .modal-overlay { padding: 16px 12px; }
            .modal-box { max-width: 100%; }
            .modal-box.modal-sm { max-width: 100%; }
            .modal-head { padding: 12px 14px; }
            .modal-body { padding: 14px; }
            .modal-foot {
                padding: 12px 14px; flex-wrap: wrap; gap: 8px;
            }
            .modal-foot .btn { flex: 1 1 auto; justify-content: center; }
            .modal-confirm-body { padding: 22px 16px 4px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
            .filter-bar { padding: 12px; }
            .filter-bar input, .filter-bar select { min-width: 0; width: 100%; }
            .filter-bar .filter-field { flex: 1 1 100%; }
            .filter-bar .filter-actions { width: 100%; margin-left: 0; }
            .page-actions { flex-wrap: wrap; }
            .page-actions .btn { flex: 1 1 auto; justify-content: center; }

            /* Modal casi a pantalla completa en celulares chicos */
            .modal-overlay { padding: 8px 6px; }
            .modal-foot { padding: 10px 12px; }
            .modal-body { padding: 12px; }

            /* Paginacion mas compacta */
            .pagination-wrap { padding: 10px 12px; }
            .pagination .page-link { min-width: 30px; height: 30px; font-size: 12px; padding: 0 7px; }
            .pagination { flex-wrap: wrap; gap:6px; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- SIDEBAR --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="ti ti-flame"></i></div>
            <div>
                <div class="brand-name">FuelControl</div>
                <div class="brand-sub">Gestion de combustible</div>
            </div>
        </div>

        @php $esAdmin = auth()->check() && auth()->user()->isAdmin(); @endphp

        <nav>
            <div class="nav-section">
                <div class="nav-section-label">Principal</div>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-layout-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('consumos.index') }}" class="nav-item {{ request()->routeIs('consumos.*') ? 'active' : '' }}">
                    <i class="ti ti-gas-station"></i> Consumos
                </a>
                <a href="{{ route('boletas.index') }}" class="nav-item {{ request()->routeIs('boletas.*') ? 'active' : '' }}">
                    <i class="ti ti-receipt"></i> Boletas
                </a>
            </div>

            @if($esAdmin)
                <div class="nav-section">
                    <div class="nav-section-label">Reportes</div>
                    <a href="{{ route('reportes.vehiculos') }}" class="nav-item {{ request()->routeIs('reportes.vehiculos') ? 'active' : '' }}">
                        <i class="ti ti-chart-bar"></i> Por vehiculo
                    </a>
                    <a href="{{ route('reportes.tramos') }}" class="nav-item {{ request()->routeIs('reportes.tramos') ? 'active' : '' }}">
                        <i class="ti ti-route"></i> Por tramo
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-label">Maestros</div>
                    <a href="{{ route('vehiculos.index') }}" class="nav-item {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                        <i class="ti ti-truck"></i> Vehiculos
                    </a>
                    <a href="{{ route('tramos.index') }}" class="nav-item {{ request()->routeIs('tramos.*') ? 'active' : '' }}">
                        <i class="ti ti-map-pin"></i> Tramos
                    </a>
                    <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                        <i class="ti ti-users"></i> Trabajadores
                    </a>
                </div>
            @endif
        </nav>

        @auth
            <div class="sidebar-footer">
                <div class="user-box">
                    <div class="user-avatar">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="user-meta">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->isAdmin() ? 'Administrador' : 'Trabajador' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn"><i class="ti ti-logout"></i> Cerrar sesion</button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- Overlay oscuro detras del sidebar en movil --}}
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- MAIN --}}
    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="menu-toggle" onclick="toggleSidebar()" aria-label="Abrir menu">
                    <i class="ti ti-menu-2"></i>
                </button>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <span class="topbar-badge">@yield('page-badge', 'General')</span>
            </div>
            <div class="topbar-right">
                <span class="topbar-date">{{ now()->format('d/m/Y') }}</span>
            </div>
        </header>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ti ti-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="ti ti-alert-triangle"></i>
                    Revisa los campos del formulario: {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    {{-- MODAL DE CONFIRMACION (global) --}}
    <div class="modal-overlay" id="confirm-modal">
        <div class="modal-box modal-sm">
            <div class="modal-confirm-body">
                <div class="modal-confirm-icon"><i class="ti ti-help-circle"></i></div>
                <div class="modal-confirm-text" id="confirm-message">¿Estas seguro?</div>
                <div class="modal-confirm-sub">Esta accion debe confirmarse para continuar.</div>
            </div>
            <div class="modal-foot centered">
                <button type="button" class="btn" onclick="confirmNo()"><i class="ti ti-x"></i> No</button>
                <button type="button" class="btn btn-primary" onclick="confirmYes()"><i class="ti ti-check"></i> Si, continuar</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            var m = document.getElementById(id);
            if (m) m.classList.add('open');
        }
        function closeModal(id) {
            var m = document.getElementById(id);
            if (m) m.classList.remove('open');
        }

        // Sidebar movil: abrir/cerrar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('open');
        }
        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('open');
        }
        // En movil, cerrar el sidebar al tocar cualquier enlace de navegacion
        document.querySelectorAll('.sidebar .nav-item').forEach(function (el) {
            el.addEventListener('click', function () {
                if (window.innerWidth <= 860) closeSidebar();
            });
        });

        // Confirmacion "¿Esta seguro?" para cualquier form con data-confirm
        var pendingForm = null;
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.dataset || !form.dataset.confirm || form.dataset.confirmed === '1') return;
            e.preventDefault();
            // Respeta la validacion del navegador (campos requeridos, etc.)
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                form.reportValidity();
                return;
            }
            pendingForm = form;
            document.getElementById('confirm-message').textContent = form.dataset.confirm;
            openModal('confirm-modal');
        });
        function confirmYes() {
            if (!pendingForm) return;
            pendingForm.dataset.confirmed = '1';
            pendingForm.submit();
        }
        function confirmNo() {
            pendingForm = null;
            closeModal('confirm-modal');
        }

        // Cerrar al hacer clic fuera del cuadro o con la tecla Escape
        document.querySelectorAll('.modal-overlay').forEach(function (ov) {
            ov.addEventListener('mousedown', function (e) {
                if (e.target === ov) ov.classList.remove('open');
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.open').forEach(function (m) {
                    m.classList.remove('open');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
