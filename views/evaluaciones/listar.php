<?php
$titulo_pagina = 'Evaluaciones de Tutoría';
ob_start();
?>

<h1>Evaluaciones</h1>
<a href="index.php?accion=evaluacion_crear" class="btn btn-primario">+ Nueva Evaluación</a>

<table>
    <tr>
        <th>ID</th>
        <th>Tutoría</th>
        <th>Calificación</th>
        <th>Comentario</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($evaluaciones as $e): ?>
    <tr>
        <td><?= $e['id_evaluacion'] ?></td>
        <td>Tutoría #<?= $e['id_tutoria'] ?></td>
        <td><?= str_repeat('⭐', $e['calificacion']) ?> (<?= $e['calificacion'] ?>/5)</td>
        <td><?= htmlspecialchars(substr($e['comentario'] ?? '-', 0, 40)) ?>...</td>
        <td><?= date('d/m/Y H:i', strtotime($e['fecha_evaluacion'])) ?></td>
        <td>
            <a href="index.php?accion=evaluacion_editar&id=<?= $e['id_evaluacion'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=evaluacion_eliminar&id=<?= $e['id_evaluacion'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';