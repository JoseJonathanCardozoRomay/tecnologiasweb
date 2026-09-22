<?php
$titulo_pagina = 'Registrar Acceso';
ob_start();
?>

<h1>Registrar Acceso Manual</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=accesos_crear">
    <label>Usuario:</label>
    <select name="id_usuario">
        <option value="">Sin asignar</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Dirección IP:</label>
    <input type="text" name="ip_origen" placeholder="Ej: 192.168.1.1">

    <label>Resultado:</label>
    <select name="resultado" required>
        <option value="exitoso">✅ Exitoso</option>
        <option value="fallido">❌ Fallido</option>
    </select>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=accesos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';