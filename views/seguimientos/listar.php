<?php
$titulo_pagina = 'Seguimiento de Sesiones';
ob_start();
?>

<h1>Seguimiento de Tutorías</h1>
<a href="index.php?accion=seguimiento_crear" class="btn btn-primario">+ Nuevo Seguimiento</a>

<table>
    <tr>
        <th>ID</th>
        <th>Tutoría</th>
        <th>Asistencia</th>
        <th>Avance</th>
        <th>Fecha</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($seguimientos as $s): ?>
    <tr>
        <td><?= $s['id_seguimiento'] ?></td>
        <td>Tutoría #<?= $s['id_tutoria'] ?></td>
        <td><?= $s['asistio'] === 'si' ? '✅ Asistió' : '❌ No asistió' ?></td>
        <td>
            <?php
            $avances = ['sin_avance' => 'Sin avance', 'parcial' => 'Parcial', 'logrado' => 'Logrado'];
            echo $avances[$s['avance']] ?? $s['avance'];
            ?>
        </td>
        <td><?= date('d/m/Y', strtotime($s['fecha_registro'])) ?></td>
        <td>
            <a href="index.php?accion=seguimiento_editar&id=<?= $s['id_seguimiento'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=seguimiento_eliminar&id=<?= $s['id_seguimiento'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';