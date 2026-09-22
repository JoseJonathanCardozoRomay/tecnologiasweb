<?php
$titulo_pagina = 'Notificaciones';
ob_start();
?>

<h1>Notificaciones</h1>
<a href="index.php?accion=notificacion_crear" class="btn btn-primario">+ Nueva Notificación</a>

<table>
    <tr>
        <th>ID</th>
        <th>Para</th>
        <th>Tipo</th>
        <th>Mensaje</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($notificaciones as $n): ?>
    <tr>
        <td><?= $n['id_notificacion'] ?></td>
        <td><?= htmlspecialchars($n['nombre_usuario'] ?? '') ?></td>
        <td><?= htmlspecialchars($n['tipo']) ?></td>
        <td><?= htmlspecialchars(substr($n['mensaje'], 0, 35)) ?>...</td>
        <td><?= $n['leida'] ? '✅ Leída' : '🔴 No leída' ?></td>
        <td><?= date('d/m/Y H:i', strtotime($n['fecha_creacion'])) ?></td>
        <td>
            <a href="index.php?accion=notificacion_editar&id=<?= $n['id_notificacion'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=notificacion_eliminar&id=<?= $n['id_notificacion'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';