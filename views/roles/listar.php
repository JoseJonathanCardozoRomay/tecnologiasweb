<?php
$titulo_pagina = 'Listado de Roles';
ob_start();
?>

<h1>Listado de Roles</h1>
<a href="index.php?accion=rol_crear" class="btn btn-primario">+ Nuevo Rol</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre del Rol</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($roles as $r): ?>
    <tr>
        <td><?= $r['id_rol'] ?></td>
        <td><?= htmlspecialchars($r['nombre_rol']) ?></td>
        <td>
            <a href="index.php?accion=rol_editar&id=<?= $r['id_rol'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=rol_eliminar&id=<?= $r['id_rol'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro de eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';