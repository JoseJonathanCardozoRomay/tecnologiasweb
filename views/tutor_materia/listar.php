<?php
$titulo_pagina = 'Materias que domina cada Tutor';
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
    <?php if (!empty($asignaciones)): ?>
        <?php foreach ($asignaciones as $a): ?>
        <tr>
            <td>
                <?php 
                $nombre_completo = trim(($a['nombre_tutor'] ?? '') . ' ' . ($a['apellido_tutor'] ?? ''));
                echo $nombre_completo ?: 'Sin nombre';
                ?>
            </td>
            <td><?= htmlspecialchars($a['nombre_materia'] ?? 'Sin materia') ?></td>
            <td>
                <a href="index.php?accion=tutor_materia_eliminar&id_tutor=<?= $a['id_tutor'] ?>&id_materia=<?= $a['id_materia'] ?>" 
                   class="btn btn-peligro" 
                   onclick="return confirm('¿Quitar esta materia?')">Quitar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3" style="text-align:center; padding:20px;">
                No hay asignaciones. <br>
                Presiona "+ Asignar Materia" para agregar.
            </td>
        </tr>
    <?php endif; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';