<?php
if (session_status() === PHP_SESSION_NONE) {
  require_once __DIR__ . '/../../includes/sesion.php';
}
if (isset($_SESSION['id_usuario'])) {
    $destinos = [
      'administrador' => '../../controllers/usuarios_listar.php',
      'tutor' => '../tutor/panel.php',
      'estudiante' => '../estudiante/panel.php',
    ];
    header('Location: ' . ($destinos[$_SESSION['rol'] ?? ''] ?? '../../controllers/usuarios_listar.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#002b49">
  <title>Iniciar Sesión - Sistema de Tutorías UPDS</title>
  <!-- Reemplazar el logo provisional de assets/img/logo-upds.svg por el logo oficial cuando esté disponible. -->
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/upds-theme.css">
</head>
<body class="login-page">
  <main class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
      <section class="col-lg-6 login-brand-panel" aria-labelledby="loginBrandTitle">
        <div class="login-brand-copy">
          <img class="login-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
          <div class="login-kicker">Universidad Privada Domingo Savio · Sede Tarija</div>
          <h1 id="loginBrandTitle" class="login-title">Sistema de Tutorías Académicas</h1>
          <p class="login-lead">Un espacio institucional para conectar estudiantes y tutores con acompañamiento académico oportuno.</p>
          <ul class="login-benefits" aria-label="Beneficios del sistema">
            <li><i class="bi bi-calendar2-check" aria-hidden="true"></i>Agenda tutorías en pocos pasos.</li>
            <li><i class="bi bi-graph-up-arrow" aria-hidden="true"></i>Da seguimiento a cada solicitud.</li>
            <li><i class="bi bi-bar-chart-line" aria-hidden="true"></i>Consulta reportes académicos claros.</li>
          </ul>
        </div>
      </section>
      <section class="col-lg-6 login-form-panel" aria-labelledby="loginTitle">
        <div class="login-form-wrap">
          <img class="login-form-logo" src="/assets/img/logo-upds.svg" alt="UPDS">
          <p class="login-kicker">Acceso institucional</p>
          <h2 id="loginTitle" class="login-form-title mb-2">Iniciar sesión</h2>
          <p class="login-form-subtitle mb-4">Ingresa con tus credenciales para continuar.</p>

          <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-4" role="alert">
              <i class="bi bi-exclamation-triangle-fill flex-shrink-0" aria-hidden="true"></i>
              <div><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            </div>
            <?php unset($_SESSION['login_error']); ?>
          <?php endif; ?>

          <form action="../../controllers/login_procesar.php" method="POST" autocomplete="off">
            <?php require_once __DIR__ . '/../../includes/csrf.php'; echo csrf_campo(); ?>
            <div class="mb-3">
              <label for="usuarioInput" class="form-label">Usuario o correo electrónico</label>
              <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person" aria-hidden="true"></i></span>
                <input type="text" class="form-control" id="usuarioInput" name="usuario" placeholder="Ej.: usuario o correo" autocomplete="username" required autofocus>
              </div>
            </div>
            <div class="mb-4">
              <label for="passwordInput" class="form-label">Contraseña</label>
              <div class="password-wrap">
                <input type="password" class="form-control" id="passwordInput" name="contrasena" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
                <button type="button" class="btn btn-link password-toggle p-2" id="togglePassword" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                  <i class="bi bi-eye" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center gap-2">
              <span>Ingresar</span><i class="bi bi-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
          <p class="text-center mt-3 mb-0"><span class="text-muted small" aria-disabled="true">¿Olvidaste tu contraseña?</span></p>
          <div class="text-center mt-3"><a href="/controllers/registro.php">Crear cuenta de estudiante</a></div>
          <p class="text-muted small text-center mt-4 mb-0">Tecnologías Web · Sede Tarija</p>
        </div>
      </section>
    </div>
  </main>
  <script>
    const passwordInput = document.getElementById('passwordInput');
    const togglePassword = document.getElementById('togglePassword');
    togglePassword?.addEventListener('click', () => {
      const visible = passwordInput.type === 'text';
      passwordInput.type = visible ? 'password' : 'text';
      togglePassword.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
      togglePassword.setAttribute('title', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
      togglePassword.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  </script>
</body>
</html>
