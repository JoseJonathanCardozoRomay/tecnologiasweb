<?php
$titulo_pagina = 'Gestión de Tutorías';
ob_start();
?>

<h1>Listado de Tutorías</h1>
<a href="index.php?accion=tutoria_crear" class="btn btn-primario">+ Nueva Tutoría</a>

<table>
    <tr>
        <th>ID</th>
        <th>Estudiante</th>
        <th>Tutor</th>
        <th>Materia</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($tutorias as $t): ?>
    <tr>
        <td><?= $t['id_tutoria'] ?></td>
        <td><?= htmlspecialchars($t['nombre_estudiante'] ?? '') ?></td>
        <td><?= htmlspecialchars($t['nombre_tutor'] ?? '') ?></td>
        <td><?= htmlspecialchars($t['nombre_materia'] ?? '') ?></td>
        <td><?= date('d/m/Y', strtotime($t['fecha'])) ?> <?= date('H:i', strtotime($t['hora_inicio'])) ?></td>
        <td>
            <?php
            $estados = ['pendiente' => '⏳ Pendiente', 'confirmada' => '✅ Confirmada', 'realizada' => '✔️ Realizada', 'cancelada' => '❌ Cancelada'];
            echo $estados[$t['estado']] ?? $t['estado'];
            ?>
        </td>
        <td>
            <a href="index.php?accion=tutoria_editar&id=<?= $t['id_tutoria'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=tutoria_eliminar&id=<?= $t['id_tutoria'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';