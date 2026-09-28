<?php
$titulo_pagina = 'Enviar Notificación';
ob_start();
?>
<h1>Enviar Nueva Notificación</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=notificacion_crear">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Destinatario:</label>
    <select name="id_usuario" required>
        <option value="">Seleccione usuario</option>
        <?php foreach ($usuarios as $u): ?>
        <option value="<?= (int)$u['id_usuario'] ?>">
            <?= htmlspecialchars(($u['nombre'] ?? '') . ' ' . ($u['apellido'] ?? '')) ?> — <?= htmlspecialchars($u['usuario'] ?? '') ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <label>Tipo de Notificación:</label>
    <input type="text" name="tipo" placeholder="Ej: Recordatorio, Confirmación, Aviso..." required>
    
    <label>Mensaje:</label>
    <textarea name="mensaje" rows="4" placeholder="Escribe el mensaje..." required></textarea>
    
    <label>Enlace (opcional):</label>
    <input type="url" name="url" placeholder="https://...">
    
    <button type="submit" class="btn btn-primario">Enviar Notificación</button>
    <a href="index.php?accion=notificaciones_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';