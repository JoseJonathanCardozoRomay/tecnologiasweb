<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rolSesion = $_SESSION['rol'] ?? '';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
$apellidoSesion = $_SESSION['apellido'] ?? '';
$usuarioSesion = $_SESSION['usuario'] ?? '';
$iniciales = strtoupper(substr($nombreSesion, 0, 1) . substr($apellidoSesion, 0, 1));
$tituloPagina = $tituloPagina ?? 'Sistema de Tutorías - UPDS';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloPagina ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f4f6f9;
            color: #334155;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: #0a1628;
            color: white;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
        }
        .sidebar-logo i {
            font-size: 1.75rem;
            color: #4a9eff;
        }
        .sidebar-logo .logo-text {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .sidebar-logo .logo-text span {
            display: block;
            font-size: 0.6rem;
            font-weight: 400;
            color: rgba(255,255,255,0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
        }
        .nav-section {
            margin-bottom: 1.5rem;
        }
        .nav-section-title {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.35);
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.5rem;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .nav-item:hover {
            color: white;
            background: rgba(255,255,255,0.05);
            border-left-color: #4a9eff;
        }
        .nav-item.active {
            color: white;
            background: rgba(74,158,255,0.15);
            border-left-color: #4a9eff;
        }
        .nav-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            color: white;
        }
        .user-details {
            flex: 1;
            min-width: 0;
        }
        .user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role {
            font-size: 0.65rem;
            color: rgba(255,255,255,0.5);
        }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.5rem;
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-logout:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        /* Main content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .top-bar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .top-bar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }
        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .content-wrapper {
            flex: 1;
            padding: 2rem;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            background: #fff;
        }
        .badge-admin { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-tutor { background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
        .badge-estudiante { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>

<?php if (isset($_SESSION['id_usuario'])): ?>
<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/panel.php" class="sidebar-logo">
            <i class="bi bi-mortarboard-fill"></i>
            <div class="logo-text">
                Sistema de Tutorías
                <span>Universidad Privada Domingo Savio</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        <?php if ($rolSesion === 'administrador'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Gestión Académica</div>
            <a class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active' : '' ?>" href="/controllers/usuarios_listar.php">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <a class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'carreras') !== false ? 'active' : '' ?>" href="/controllers/carreras_listar.php">
                <i class="bi bi-mortarboard"></i> Carreras
            </a>
            <a class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'materias') !== false ? 'active' : '' ?>" href="/controllers/materias_listar.php">
                <i class="bi bi-journal-bookmark"></i> Materias
            </a>
            <a class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'tutores') !== false ? 'active' : '' ?>" href="/controllers/TutorController.php?action=listar">
                <i class="bi bi-person-video3"></i> Tutores
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-title">Tutorías</div>
            <a class="nav-item" href="#" onclick="Swal.fire('Próximo Módulo', 'Módulo de Tutorías en desarrollo', 'info'); return false;">
                <i class="bi bi-calendar-check"></i> Tutorías
            </a>
            <a class="nav-item" href="#" onclick="Swal.fire('Próximo Módulo', 'Asignación de tutorías en desarrollo', 'info'); return false;">
                <i class="bi bi-plus-circle"></i> Asignar tutoría
            </a>
            <a class="nav-item" href="#" onclick="Swal.fire('Próximo Módulo', 'Tribunales en desarrollo', 'info'); return false;">
                <i class="bi bi-award"></i> Tribunales
            </a>
        </div>
        <?php elseif ($rolSesion === 'tutor'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Gestión</div>
            <a class="nav-item active" href="/views/tutor/panel.php">
                <i class="bi bi-calendar-range"></i> Mis Horarios
            </a>
            <a class="nav-item" href="/views/tutor/panel.php">
                <i class="bi bi-card-checklist"></i> Sesiones Asignadas
            </a>
        </div>
        <?php else: ?>
        <div class="nav-section">
            <div class="nav-section-title">Tutorías</div>
            <a class="nav-item active" href="/views/estudiante/panel.php">
                <i class="bi bi-search"></i> Buscar Tutorías
            </a>
            <a class="nav-item" href="/views/estudiante/panel.php">
                <i class="bi bi-clock-history"></i> Mis Solicitudes
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= htmlspecialchars($iniciales) ?></div>
            <div class="user-details">
                <div class="user-name"><?= htmlspecialchars($nombreSesion) ?></div>
                <div class="user-role"><?= htmlspecialchars($rolSesion) ?></div>
            </div>
        </div>
        <a href="/controllers/logout.php" class="btn-logout">
            <i class="bi bi-box-arrow-right"></i> Salir
        </a>
    </div>
</aside>
<?php endif; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="top-bar">
        <div class="top-bar-title"><?= $tituloPagina ?></div>
        <div class="top-bar-actions">
            <span class="text-muted small"><?= htmlspecialchars($nombreSesion) ?></span>
        </div>
    </div>
    <div class="content-wrapper">
