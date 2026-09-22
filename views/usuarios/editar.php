<?php
$titulo_pagina = 'Editar Usuario';
ob_start();
?>

<h1>Editar Usuario</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=usuario_editar&id=<?= $usuario['id_usuario'] ?>">
    <label>Rol:</label>
    <select name="id_rol" required>
        <?php foreach ($roles as $r): ?>
        <option value="<?= $r['id_rol'] ?>" <?= $r['id_rol'] == $usuario['id_rol'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($r['nombre_rol']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Nombre:</label>
    <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

    <label>Apellido:</label>
    <input type="text" name="apellido" value="<?= htmlspecialchars($usuario['apellido']) ?>" required>

    <label>Correo:</label>
    <input type="email" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required>

    <label>Usuario:</label>
    <input type="text" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">

    <label>Estado:</label>
    <select name="estado">
        <option value="activo" <?= ($usuario['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
        <option value="inactivo" <?= ($usuario['estado'] ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
    </select>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=usuarios_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';