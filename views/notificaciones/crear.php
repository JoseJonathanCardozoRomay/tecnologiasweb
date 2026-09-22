<?php
$titulo_pagina = 'Enviar Notificación';
ob_start();
?>

<h1>Nueva Notificación</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=notificacion_crear">
    <label>Destinatario:</label>
    <select name="id_usuario" required>
        <option value="">Seleccione</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= $u['id_usuario'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Tipo:</label>
    <input type="text" name="tipo" placeholder="Ej: recordatorio, aviso, mensaje" required>

    <label>Mensaje:</label>
    <textarea name="mensaje" rows="4" placeholder="Escribe el mensaje..." required></textarea>

    <label>Enlace (opcional):</label>
    <input type="text" name="url" placeholder="Ej: index.php?accion=tutorias_listar">

    <button type="submit" class="btn btn-primario">Enviar</button>
    <a href="index.php?accion=notificaciones_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';