<?php
require_once __DIR__ . '/../../includes/sesion.php';
require_once __DIR__ . '/../../includes/csrf.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear cuenta - Sistema de Tutorías UPDS</title>
  <link rel="icon" href="/assets/img/favicon.svg" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/assets/css/upds-theme.css">
</head>
<body class="login-page py-4">
  <main class="container">
    <div class="card card-custom mx-auto p-4 p-md-5" style="max-width: 850px;">
      <div class="text-center mb-4">
        <img src="/assets/img/logo-upds.svg" alt="UPDS" class="login-form-logo">
        <h1 class="login-form-title">Crear cuenta de estudiante</h1>
        <p class="login-form-subtitle">Completa tus datos académicos para solicitar acceso.</p>
      </div>
      <?php if (!empty($errores['general'])): ?><div class="alert alert-danger"><?= htmlspecialchars($errores['general']) ?></div><?php endif; ?>
      <form method="POST" novalidate>
        <?= csrf_campo() ?>
        <input type="text" name="sitio_web" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label" for="nombre">Nombre</label><input class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" maxlength="100" required><?= !empty($errores['nombre']) ? '<div class="text-danger small">' . htmlspecialchars($errores['nombre']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="apellido">Apellido</label><input class="form-control" id="apellido" name="apellido" value="<?= htmlspecialchars($datos['apellido']) ?>" maxlength="100" required><?= !empty($errores['apellido']) ? '<div class="text-danger small">' . htmlspecialchars($errores['apellido']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="correo">Correo electrónico</label><input type="email" class="form-control" id="correo" name="correo" value="<?= htmlspecialchars($datos['correo']) ?>" maxlength="150" required><?= !empty($errores['correo']) ? '<div class="text-danger small">' . htmlspecialchars($errores['correo']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="usuario">Usuario</label><input class="form-control" id="usuario" name="usuario" value="<?= htmlspecialchars($datos['usuario']) ?>" minlength="4" maxlength="50" pattern="[a-z0-9._-]+" required><?= !empty($errores['usuario']) ? '<div class="text-danger small">' . htmlspecialchars($errores['usuario']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="clave">Contraseña</label><input type="password" class="form-control" id="clave" name="clave" minlength="8" required><?= !empty($errores['clave']) ? '<div class="text-danger small">' . htmlspecialchars($errores['clave']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="confirmar_clave">Confirmar contraseña</label><input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="8" required><?= !empty($errores['confirmar_clave']) ? '<div class="text-danger small">' . htmlspecialchars($errores['confirmar_clave']) . '</div>' : '' ?></div>
          <div class="col-md-6"><label class="form-label" for="id_carrera">Carrera</label><select class="form-select" id="id_carrera" name="id_carrera" required><option value="">Selecciona una carrera</option><?php foreach ($carreras as $carrera): ?><option value="<?= $carrera['id_carrera'] ?>" <?= $datos['id_carrera'] == $carrera['id_carrera'] ? 'selected' : '' ?>><?= htmlspecialchars($carrera['nombre_carrera']) ?></option><?php endforeach; ?></select><?= !empty($errores['id_carrera']) ? '<div class="text-danger small">' . htmlspecialchars($errores['id_carrera']) . '</div>' : '' ?></div>
          <div class="col-md-3"><label class="form-label" for="semestre">Semestre</label><input type="number" class="form-control" id="semestre" name="semestre" min="1" max="12" value="<?= htmlspecialchars($datos['semestre']) ?>" required><?= !empty($errores['semestre']) ? '<div class="text-danger small">' . htmlspecialchars($errores['semestre']) . '</div>' : '' ?></div>
          <div class="col-md-3"><label class="form-label" for="registro_universitario">R.U.</label><input class="form-control" id="registro_universitario" name="registro_universitario" maxlength="30" value="<?= htmlspecialchars($datos['registro_universitario']) ?>" required><?= !empty($errores['registro_universitario']) ? '<div class="text-danger small">' . htmlspecialchars($errores['registro_universitario']) . '</div>' : '' ?></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="/views/login/login.php" class="btn btn-light">Cancelar</a><button class="btn btn-primary" type="submit">Crear cuenta</button></div>
      </form>
    </div>
  </main>
</body>
</html>
