<?php
$titulo_pagina = 'Listado de Materias';
ob_start();
?>

<h1>Listado de Materias</h1>
<a href="index.php?accion=materia_crear" class="btn btn-primario">+ Nueva Materia</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre de la Materia</th>
        <th>Carrera</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($materias as $m): ?>
    <tr>
        <td><?= $m['id_materia'] ?></td>
        <td><?= htmlspecialchars($m['nombre_materia']) ?></td>
        <td><?= htmlspecialchars($m['nombre_carrera'] ?? 'General') ?></td>
        <td>
            <a href="index.php?accion=materia_editar&id=<?= $m['id_materia'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=materia_eliminar&id=<?= $m['id_materia'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';