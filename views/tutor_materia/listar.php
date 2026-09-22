<?php
$titulo_pagina = 'Relación Tutor - Materia';
ob_start();
?>

<h1>Materias que domina cada Tutor</h1>
<a href="index.php?accion=tutor_materia_crear" class="btn btn-primario">+ Asignar Materia</a>

<table>
    <tr>
        <th>Tutor</th>
        <th>Materia</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($asignaciones as $a): ?>
    <tr>
        <td><?= htmlspecialchars(($a['nombre_tutor'] ?? '') . ' ' . ($a['apellido_tutor'] ?? '')) ?></td>
        <td><?= htmlspecialchars($a['nombre_materia'] ?? '') ?></td>
        <td>
            <a href="index.php?accion=tutor_materia_eliminar&id_tutor=<?= $a['id_tutor'] ?>&id_materia=<?= $a['id_materia'] ?>" class="btn btn-peligro" onclick="return confirm('¿Quitar esta materia al tutor?')">Quitar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';