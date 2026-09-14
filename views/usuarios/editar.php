<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar usuario</title>
</head>
<body>
  <h1>Editar usuario</h1>

  <?php foreach ($errores as $e): ?>
    <p style="color:red;"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>

  <form method="POST">
    <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($usuario_actual['id_usuario']) ?>">

    <label>Rol:
      <select name="id_rol" required>
        <?php foreach ($roles as $r): ?>
          <option value="<?= $r['id_rol'] ?>" <?= $r['id_rol'] == $usuario_actual['id_rol'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($r['nombre_rol']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label><br><br>

    <label>Nombre: <input type="text" name="nombre" value="<?= htmlspecialchars($usuario_actual['nombre']) ?>" required></label><br><br>
    <label>Apellido: <input type="text" name="apellido" value="<?= htmlspecialchars($usuario_actual['apellido']) ?>" required></label><br><br>
    <label>Correo: <input type="email" name="correo" value="<?= htmlspecialchars($usuario_actual['correo']) ?>" required></label><br><br>
    <label>Usuario: <input type="text" name="usuario" value="<?= htmlspecialchars($usuario_actual['usuario']) ?>" required></label><br><br>
    <label>Estado:
      <select name="estado">
        <option value="activo"   <?= $usuario_actual['estado'] === 'activo'   ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= $usuario_actual['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
      </select>
    </label><br><br>

    <button type="submit">Actualizar</button>
    <a href="usuarios_listar.php">Cancelar</a>
  </form>
</body>
</html>