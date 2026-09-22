<?php
$titulo_pagina = 'Listado de Estudiantes';
ob_start();
?>

<h1>Listado de Estudiantes</h1>
<a href="index.php?accion=estudiante_crear" class="btn btn-primario">+ Nuevo Estudiante</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Carrera</th>
        <th>Semestre</th>
        <th>Registro Universitario</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($estudiantes as $e): ?>
    <tr>
        <td><?= $e['id_estudiante'] ?></td>
        <td><?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?></td>
        <td><?= htmlspecialchars($e['nombre_carrera'] ?? '') ?></td>
        <td><?= $e['semestre'] ?></td>
        <td><?= htmlspecialchars($e['registro_universitario'] ?? '') ?></td>
        <td>
            <a href="index.php?accion=estudiante_editar&id=<?= $e['id_estudiante'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=estudiante_eliminar&id=<?= $e['id_estudiante'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';