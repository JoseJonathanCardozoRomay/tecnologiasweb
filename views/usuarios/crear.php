<?php require_once __DIR__ . '/../../includes/verificar_sesion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Nuevo usuario</title>
</head>
<body>
  <h1>Registrar nuevo usuario</h1>

  <?php foreach ($errores as $e): ?>
    <p style="color:red;"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>

  <form method="POST">
    <label>Rol:
      <select name="id_rol" required>
        <?php foreach ($roles as $r): ?>
          <option value="<?= $r['id_rol'] ?>"><?= htmlspecialchars($r['nombre_rol']) ?></option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Nombre: <input type="text" name="nombre" required></label><br><br>
    <label>Apellido: <input type="text" name="apellido" required></label><br><br>
    <label>Correo: <input type="email" name="correo" required></label><br><br>
    <label>Usuario: <input type="text" name="usuario" required></label><br><br>
    <label>Contraseña: <input type="password" name="clave" required minlength="6"></label><br><br>

    <button type="submit">Guardar</button>
    <a href="usuarios_listar.php">Cancelar</a>
  </form>
</body>
</html>