<?php
$titulo_pagina = 'Listado de Usuarios';
ob_start();
?>

<h1>Listado de Usuarios</h1>
<a href="index.php?accion=usuario_crear" class="btn btn-primario">+ Nuevo Usuario</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($usuarios as $u): ?>
    <tr>
        <td><?= $u['id_usuario'] ?></td>
        <td><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
        <td><?= htmlspecialchars($u['usuario']) ?></td>
        <td><?= htmlspecialchars($u['nombre_rol']) ?></td>
        <td><?= $u['estado'] === 'activo' ? '✅ Activo' : '❌ Inactivo' ?></td>
        <td>
            <a href="index.php?accion=usuario_editar&id=<?= $u['id_usuario'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=usuario_eliminar&id=<?= $u['id_usuario'] ?>" class="btn btn-peligro" onclick="return confirm('¿Seguro?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';