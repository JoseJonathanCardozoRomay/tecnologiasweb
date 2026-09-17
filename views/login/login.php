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
  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: linear-gradient(135deg, #0a192f 0%, #1e3a5f 50%, #0d2137 100%);
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
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }
    .btn-login {
      background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
      border: none;
      font-weight: 600;
      letter-spacing: 0.3px;
    }
    .btn-login:hover {
      background: linear-gradient(135deg, #0b5ed7 0%, #0a58ca 100%);
    }
  </style>
</head>
<body>

<div class="login-card p-4 p-md-5">
  <div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 68px; height: 68px;">
      <i class="bi bi-mortarboard-fill fs-1"></i>
    </div>
    <h3 class="fw-bold text-dark mb-1">Sistema de Tutorías</h3>
    <p class="text-muted small">Universidad Privada Domingo Savio</p>
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