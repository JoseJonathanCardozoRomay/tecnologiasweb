<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Si ya está logueado, redirigir
if (isset($_SESSION['id_usuario'])) {
    header('Location: ../../controllers/usuarios_listar.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión - Sistema de Tutorías UPDS</title>
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      background: linear-gradient(135deg, #001e3d 0%, #002b49 55%, #00152c 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
    }
    .login-card {
      max-width: 420px;
      width: 100%;
      background: #ffffff;
      border: none;
      border-radius: 10px;
      border-top: 5px solid #f5a623;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }
    .btn-login {
      background: linear-gradient(135deg, #002b49 0%, #001e3d 100%);
      border: none;
      font-weight: 600;
      letter-spacing: 0.3px;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #003b63 0%, #002855 100%);
    }
  </style>
</head>
<body>

<div class="login-card p-4 p-md-5">
  <div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 fw-extrabold" style="width: 72px; height: 72px; background: #f5a623; color: #002b49; font-size: 1.35rem;">
      UPDS
    </div>
    <h3 class="fw-bold text-dark mb-1">Sistema de Tutorías</h3>
    <p class="text-muted small mb-0">Sede Tarija • Sistema de Tutorías</p>
  </div>

  <?php if (isset($_SESSION['login_error'])): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 rounded-3" role="alert" style="font-size: 0.9rem;">
      <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
      <div><?= htmlspecialchars($_SESSION['login_error']) ?></div>
    </div>
    <?php unset($_SESSION['login_error']); ?>
  <?php endif; ?>

  <form action="../../controllers/login_procesar.php" method="POST" autocomplete="off">
    <div class="form-floating mb-3">
      <input type="text" class="form-control rounded-3" id="usuarioInput" name="usuario" placeholder="Usuario o Correo" required autofocus>
      <label for="usuarioInput"><i class="bi bi-person me-1"></i>Usuario o Correo</label>
    </div>

    <div class="form-floating mb-4">
      <input type="password" class="form-control rounded-3" id="passwordInput" name="contrasena" placeholder="Contraseña" required>
      <label for="passwordInput"><i class="bi bi-lock me-1"></i>Contraseña</label>
    </div>

    <button type="submit" class="btn btn-login btn-primary w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
      <span>Ingresar al Sistema</span>
      <i class="bi bi-arrow-right"></i>
    </button>
  </form>

  <div class="mt-4 pt-3 border-top text-center">
    <small class="text-muted">Universidad Privada Domingo Savio &bull; Tecnologías Web</small>
  </div>
</div>

</body>
</html>