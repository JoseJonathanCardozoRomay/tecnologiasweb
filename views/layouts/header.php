<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$rolSesion = $_SESSION['rol'] ?? '';
$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $tituloPagina ?? 'Sistema de Tutorías - UPDS' ?></title>
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background-color: #f4f6f9;
      color: #334155;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background: linear-gradient(135deg, #0f2b48 0%, #1e4b7a 100%);
    }
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      background: #fff;
    }
    .badge-admin { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-tutor { background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .badge-estudiante { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  </style>
</head>
<body>

<?php if (isset($_SESSION['id_usuario'])): ?>
<!-- Navbar principal del sistema -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/controllers/usuarios_listar.php">
      <i class="bi bi-mortarboard-fill text-warning fs-4"></i>
      <span>Sistema de Tutorías</span>
      <span class="badge bg-warning text-dark ms-1 d-none d-sm-inline-block" style="font-size: 0.7rem;">UPDS</span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($rolSesion === 'administrador'): ?>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/usuarios_listar.php">
              <i class="bi bi-people-fill"></i> Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'materias') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/materias_listar.php">
              <i class="bi bi-journal-bookmark-fill"></i> Materias
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'carreras') !== false ? 'active fw-bold' : 'text-white-50' ?> d-flex align-items-center gap-1" href="/controllers/carreras_listar.php">
              <i class="bi bi-mortarboard"></i> Carreras
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white-50 d-flex align-items-center gap-1" href="#" onclick="Swal.fire('Próximo Módulo', 'Módulo de Gestión General de Tutorías en desarrollo', 'info');">
              <i class="bi bi-calendar-check-fill"></i> Tutorías
            </a>
          </li>
        <?php elseif ($rolSesion === 'tutor'): ?>
          <li class="nav-item">
            <a class="nav-link active d-flex align-items-center gap-1" href="#">
              <i class="bi bi-calendar-range"></i> Mis Horarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white-50 d-flex align-items-center gap-1" href="#">
              <i class="bi bi-card-checklist"></i> Sesiones Asignadas
            </a>
          </li>
        <?php elseif ($rolSesion === 'estudiante'): ?>
          <li class="nav-item">
            <a class="nav-link active d-flex align-items-center gap-1" href="#">
              <i class="bi bi-search"></i> Buscar Tutorías
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white-50 d-flex align-items-center gap-1" href="#">
              <i class="bi bi-clock-history"></i> Mis Solicitudes
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Perfil de usuario y botón salir -->
      <div class="d-flex align-items-center gap-3">
        <div class="text-end text-white d-none d-md-block">
          <div class="fw-semibold" style="font-size: 0.9rem;"><?= htmlspecialchars($nombreSesion) ?></div>
          <span class="badge rounded-pill text-uppercase px-2" style="font-size: 0.65rem; background: rgba(255,255,255,0.2);">
            <?= htmlspecialchars($rolSesion) ?>
          </span>
        </div>
        <a href="/controllers/logout.php" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1">
          <i class="bi bi-box-arrow-right"></i>
          <span>Salir</span>
        </a>
      </div>
    </div>
  </div>
</nav>
<?php endif; ?>

<main class="container py-4 flex-grow-1">
