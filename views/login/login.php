<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión - Sistema de Tutorías</title>
</head>
<body>
  <h1>Iniciar sesión</h1>

  <?php if (isset($_SESSION['login_error'])): ?>
    <p style="color:red;"><?= htmlspecialchars($_SESSION['login_error']) ?></p>
    <?php unset($_SESSION['login_error']); ?>
  <?php endif; ?>

  <form action="../../controllers/login_procesar.php" method="POST">
    <label>Usuario o correo:</label><br>
    <input type="text" name="usuario" required><br><br>

    <label>Contraseña:</label><br>
    <input type="password" name="contrasena" required><br><br>

    <button type="submit">Ingresar</button>
  </form>
</body>
</html>