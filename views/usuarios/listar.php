<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios - Sistema de Tutorías</title>
</head>
<body>
  <h1>Usuarios registrados</h1>
  <p><a href="usuarios_crear.php">+ Nuevo usuario</a></p>

  <table border="1" cellpadding="6" cellspacing="0">
    <tr>
      <th>ID</th><th>Nombre</th><th>Apellido</th><th>Correo</th>
      <th>Usuario</th><th>Rol</th><th>Estado</th><th>Registro</th><th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
      <td><?= htmlspecialchars($u['id_usuario']) ?></td>
      <td><?= htmlspecialchars($u['nombre']) ?></td>
      <td><?= htmlspecialchars($u['apellido']) ?></td>
      <td><?= htmlspecialchars($u['correo']) ?></td>
      <td><?= htmlspecialchars($u['usuario']) ?></td>
      <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
      <td><?= htmlspecialchars($u['estado']) ?></td>
      <td><?= htmlspecialchars($u['fecha_registro']) ?></td>
      <td>
        <a href="usuarios_editar.php?id=<?= $u['id_usuario'] ?>">Editar</a> |
        <a href="usuarios_eliminar.php?id=<?= $u['id_usuario'] ?>"
           onclick="return confirm('¿Eliminar este usuario?');">Eliminar</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($usuarios)): ?>
    <tr><td colspan="9">No hay usuarios registrados todavía.</td></tr>
    <?php endif; ?>
  </table>
</body>
</html>