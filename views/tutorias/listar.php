 <?php
$titulo_pagina = 'Listado de Tutorías';
ob_start();
?>

<h1>Listado de Tutorías</h1>

<p style="margin:15px 0;">
    <a href="index.php?accion=tutoria_crear" style="background:#0066cc; color:white; padding:8px 15px; border-radius:4px; text-decoration:none;">
        + Nueva Tutoría
    </a>
</p>

<?php $tutorias = $tutorias ?? []; ?>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:white; border-radius:6px; overflow:hidden;">
    <thead style="background:#003366; color:white;">
        <tr>
            <th>ID</th>
            <th>Estudiante</th>
            <th>Tutor</th>
            <th>Materia</th>
            <th>Fecha / Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tutorias)): ?>
        <tr>
            <td colspan="7" style="text-align:center; padding:20px; color:#666;">
                No hay tutorías registradas.
            </td>
        </tr>
        <?php else: ?>
            <?php foreach ($tutorias as $t): ?>
            <tr>
                <td><?= $t['id_tutoria'] ?></td>
                <td>
                    <?php 
                    $est = trim(($t['est_nombre'] ?? '') . ' ' . ($t['est_apellido'] ?? ''));
                    echo $est ?: '<em style="color:#999;">Sin datos</em>';
                    ?>
                </td>
                <td>
                    <?php 
                    $tut = trim(($t['tut_nombre'] ?? '') . ' ' . ($t['tut_apellido'] ?? ''));
                    echo $tut ?: '<em style="color:#999;">Sin datos</em>';
                    ?>
                </td>
                <td>
                    <?= htmlspecialchars($t['nombre_materia'] ?? '-') ?>
                </td>
                <td>
                    <?= htmlspecialchars($t['fecha'] ?? '') ?><br>
                    <small><?= htmlspecialchars($t['hora_inicio'] ?? '') ?> - <?= htmlspecialchars($t['hora_fin'] ?? '') ?></small>
                </td>
                <td>
                    <?php 
                    $est = $t['estado'] ?? '';
                    $colores = [
                        'pendiente' => '#ffcc00',
                        'confirmada' => '#009933',
                        'realizada' => '#0066cc',
                        'cancelada' => '#cc0000'
                    ];
                    $color = $colores[$est] ?? '#999';
                    ?>
                    <span style="background:<?= $color ?>; color:white; padding:3px 8px; border-radius:10px; font-size:12px;">
                        <?= ucfirst($est) ?: 'Desconocido' ?>
                    </span>
                </td>
                <td>
                    <a href="index.php?accion=tutoria_editar&id=<?= $t['id_tutoria'] ?>" style="color:#0066cc; margin-right:8px;">Editar</a>
                    <a href="index.php?accion=tutoria_eliminar&id=<?= $t['id_tutoria'] ?>" style="color:#cc0000;" onclick="return confirm('¿Eliminar esta tutoría?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';