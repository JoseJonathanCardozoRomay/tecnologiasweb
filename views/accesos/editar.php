<?php
$titulo_pagina = 'Editar Registro de Acceso';
ob_start();
?>

<h1>Editar Registro de Acceso</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=accesos_editar&id=<?= $acc['id_acceso'] ?>">
    <label>Usuario:</label>
    <select name="id_usuario">
        <option value="">Sin asignar</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>" <?= $u['id_usuario'] == $acc['id_usuario'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Dirección IP:</label>
    <input type="text" name="ip_origen" value="<?= htmlspecialchars($acc['ip_origen'] ?? '') ?>">

    <label>Resultado:</label>
    <select name="resultado" required>
        <option value="exitoso" <?= $acc['resultado'] === 'exitoso' ? 'selected' : '' ?>>✅ Exitoso</option>
        <option value="fallido" <?= $acc['resultado'] === 'fallido' ? 'selected' : '' ?>>❌ Fallido</option>
    </select>

    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=accesos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';