<?php
$titulo_pagina = 'Periodos de Tutoría';
ob_start();
?>

<h1>Listado de Periodos</h1>
<a href="index.php?accion=periodo_crear" class="btn btn-primario">+ Nuevo Periodo</a>

<table>
    <tr>
        <th>ID</th>
        <th>Código</th>
        <th>Nombre</th>
        <th>Fecha Inicio</th>
        <th>Fecha Fin</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($periodos as $p): ?>
    <tr>
        <td><?= $p['id_periodo'] ?></td>
        <td><?= htmlspecialchars($p['codigo']) ?></td>
        <td><?= htmlspecialchars($p['nombre']) ?></td>
        <td><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></td>
        <td><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
        <td><?= $p['activo'] ? '✅ Activo' : 'Inactivo' ?></td>
        <td>
            <a href="index.php?accion=periodo_editar&id=<?= $p['id_periodo'] ?>" class="btn btn-exito">Editar</a>
            <a href="index.php?accion=periodo_eliminar&id=<?= $p['id_periodo'] ?>" class="btn btn-peligro" onclick="return confirm('¿Eliminar?')">Eliminar</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';