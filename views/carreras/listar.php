<?php
$titulo_pagina = 'Listado de Carreras';
ob_start();
?>

<h1>Listado de Carreras</h1>
<a href="index.php?accion=carrera_crear" class="btn btn-primario">+ Nueva Carrera</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre de la Carrera</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($carreras as $c): ?>
    <tr>
        <td><?= $c['id_carrera'] ?></td>
        <td><?= htmlspecialchars($c['nombre_carrera']) ?></td>
        <td>
            <a href="index.php?accion=carrera_editar&id=<?= $c['id_carrera'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=carrera_eliminar&id=<?= $c['id_carrera'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';