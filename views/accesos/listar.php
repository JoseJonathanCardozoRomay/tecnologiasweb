<?php
$titulo_pagina = 'Registro de Accesos';
ob_start();
?>

<h1>Historial de Accesos</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Fecha y Hora</th>
        <th>IP</th>
        <th>Resultado</th>
    </tr>
    <?php foreach ($accesos as $a): ?>
    <tr>
        <td><?= $a['id_acceso'] ?></td>
        <td><?= htmlspecialchars($a['nombre_usuario'] ?? 'Usuario desconocido') ?></td>
        <td><?= date('d/m/Y H:i:s', strtotime($a['fecha_hora'])) ?></td>
        <td><?= htmlspecialchars($a['ip_origen'] ?? '-') ?></td>
        <td>
            <?= $a['resultado'] === 'exitoso' ? '✅ Exitoso' : '❌ Fallido' ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';