<?php
if (!isset($pendientes)) $pendientes = [];
if (!isset($aceptadas)) $aceptadas = [];
if (!isset($error)) $error = '';
if (!isset($exito)) $exito = '';

$titulo_pagina = 'Mis Materias Asignadas';
ob_start();
?>
<h1>📚 Mis Materias</h1>

<?php if (!empty($error)): ?><div class="alerta alerta-error"><?= $error ?></div><?php endif; ?>
<?php if (!empty($exito)): ?><div class="alerta alerta-exito"><?= $exito ?></div><?php endif; ?>

<!-- PENDIENTES -->
<h2 style="margin-top:30px; color:#d68000;">⏳ Solicitudes Pendientes</h2>
<?php if (empty($pendientes)): ?>
<p>No tienes solicitudes pendientes ✅</p>
<?php else: ?>
<?php foreach ($pendientes as $p): ?>
<div style="border:1px solid #ffc107; padding:15px; margin:10px 0; border-radius:8px; background:#fff9e8;">
    <strong><?= htmlspecialchars($p['nombre_materia']) ?></strong>
    <br><small>Solicitud: <?= date('d/m/Y H:i', strtotime($p['fecha_solicitud'])) ?></small>
    
    <form method="POST" action="" style="margin-top:10px;">
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        
        <button type="submit" name="accion" value="aceptar" style="background:#28a745; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">✅ Aceptar</button>
        
        <div style="margin-top:8px;">
            <textarea name="motivo_rechazo" placeholder="Motivo si rechazas..." style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc;"></textarea>
            <button type="submit" name="accion" value="rechazar" style="background:#dc3545; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; margin-top:5px;">❌ Rechazar</button>
        </div>
    </form>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- ACEPTADAS -->
<h2 style="margin-top:40px; color:#28a745;">✅ Materias Aceptadas</h2>
<?php if (empty($aceptadas)): ?>
<p>Aún no has aceptado ninguna materia</p>
<?php else: ?>
<ul style="list-style:none; padding:0;">
<?php foreach ($aceptadas as $a): ?>
<li style="padding:10px; border-bottom:1px solid #ddd;">
    ✅ <?= htmlspecialchars($a['nombre_materia']) ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<a href="index.php" class="btn btn-volver" style="margin-top:30px;">← Volver al inicio</a>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../config/plantilla.php';