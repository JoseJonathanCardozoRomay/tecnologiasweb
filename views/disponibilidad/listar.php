<?php
$titulo_pagina = 'Disponibilidad de Tutores';
ob_start();
?>

<h1>Disponibilidad Horaria</h1>
<a href="index.php?accion=disponibilidad_crear" class="btn btn-primario">+ Nueva Disponibilidad</a>

<table>
    <tr>
        <th>ID</th>
        <th>Tutor</th>
        <th>Día</th>
        <th>Hora Inicio</th>
        <th>Hora Fin</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($disponibilidades as $d): ?>
    <tr>
        <td><?= $d['id_disponibilidad'] ?></td>
        <td><?= htmlspecialchars(($d['nombre_tutor'] ?? '') . ' ' . ($d['apellido_tutor'] ?? '')) ?></td>
        <td><?= $d['dia_semana'] ?></td>
        <td><?= $d['hora_inicio'] ?></td>
        <td><?= $d['hora_fin'] ?></td>
        <td>
            <a href="index.php?accion=disponibilidad_editar&id=<?= $d['id_disponibilidad'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=disponibilidad_eliminar&id=<?= $d['id_disponibilidad'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';