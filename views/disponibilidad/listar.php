<?php
$titulo_pagina = 'Disponibilidad Horaria';
ob_start();
?>

<h1>Disponibilidad Horaria</h1>

<p style="margin:15px 0;">
    <a href="index.php?accion=disponibilidad_crear" style="background:#0066cc; color:white; padding:8px 15px; border-radius:4px; text-decoration:none;">
        + Nueva Disponibilidad
    </a>
</p>

<?php 
// Asegurar que sea un array
$disponibilidades = $disponibilidades ?? [];
?>

<table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:white; border-radius:6px; overflow:hidden;">
    <thead style="background:#003366; color:white;">
        <tr>
            <th>ID</th>
            <th>Tutor</th>
            <th>Día</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($disponibilidades)): ?>
        <tr>
            <td colspan="6" style="text-align:center; padding:20px; color:#666;">
                No hay disponibilidades registradas. <a href="index.php?accion=disponibilidad_crear" style="color:#0066cc;">Registrar la primera</a>
            </td>
        </tr>
        <?php else: ?>
            <?php foreach ($disponibilidades as $d): ?>
            <tr>
                <td><?= $d['id_disponibilidad'] ?></td>
                <td><?= htmlspecialchars($d['nombre'] . ' ' . $d['apellido']) ?></td>
                <td><?= htmlspecialchars($d['dia_semana']) ?></td>
                <td><?= htmlspecialchars($d['hora_inicio']) ?></td>
                <td><?= htmlspecialchars($d['hora_fin']) ?></td>
                <td>
                    <a href="index.php?accion=disponibilidad_editar&id=<?= $d['id_disponibilidad'] ?>" style="color:#0066cc; margin-right:8px;">Editar</a>
                    <a href="index.php?accion=disponibilidad_eliminar&id=<?= $d['id_disponibilidad'] ?>" style="color:#cc0000;" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';