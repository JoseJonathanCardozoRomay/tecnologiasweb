<?php
$titulo_pagina = 'Bloques Horarios';
ob_start();
?>

<h1>Listado de Bloques Horarios</h1>
<a href="index.php?accion=bloque_crear" class="btn btn-primario">+ Nuevo Bloque</a>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre del Bloque</th>
        <th>Hora Inicio</th>
        <th>Hora Fin</th>
        <th>Descripción</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($bloques as $b): ?>
    <tr>
        <td><?= $b['id_bloque'] ?></td>
        <td><?= htmlspecialchars($b['nombre_bloque']) ?></td>
        <td><?= $b['hora_inicio'] ?></td>
        <td><?= $b['hora_fin'] ?></td>
        <td><?= htmlspecialchars($b['descripcion'] ?? '-') ?></td>
        <td>
            <a href="index.php?accion=bloque_editar&id=<?= $b['id_bloque'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=bloque_eliminar&id=<?= $b['id_bloque'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';