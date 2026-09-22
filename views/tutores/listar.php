<?php
$titulo_pagina = 'Listado de Tutores';
ob_start();
?>

<h1>Listado de Tutores</h1>
<a href="index.php?accion=tutor_crear" class="btn btn-primario">+ Nuevo Tutor</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Especialidad</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($tutores as $t): ?>
    <tr>
        <td><?= $t['id_tutor'] ?></td>
        <td><?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?></td>
        <td><?= htmlspecialchars($t['especialidad'] ?? 'No especificada') ?></td>
        <td>
            <a href="index.php?accion=tutor_editar&id=<?= $t['id_tutor'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=tutor_eliminar&id=<?= $t['id_tutor'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';