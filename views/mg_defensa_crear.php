 <?php
if (!isset($error)) $error = '';
if (!isset($tribunales)) $tribunales = [];
if (!isset($expedientes)) $expedientes = [];

$titulo_pagina = 'Programar Nueva Defensa';
ob_start();
?>
<h1>Programar Nueva Defensa</h1>

<!-- ✅ Mensaje de error con diseño profesional -->
<?php if (!empty($error)): ?>
<div style="background: #fff8e1; border-left: 4px solid #f59e0b; color: #78350f;
            padding: 16px 20px; margin: 20px 0; border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-size: 15px; line-height: 1.7;">
    <strong>⚠️ Atención</strong><br>
    <?= $error ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=mg_defensa_crear" 
      style="max-width:650px; margin:25px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    
    <!-- ✅ Token de seguridad CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Tribunal / Jurado *</label>
        <select name="id_tribunal" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un tribunal...</option>
            <?php foreach ($tribunales as $t): ?>
            <option value="<?= (int)$t['id_tribunal'] ?>">
                <?= htmlspecialchars($t['nombre_completo'] ?? '') ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Expediente / Estudiante *</label>
        <select name="id_expediente" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un expediente...</option>
            <?php foreach ($expedientes as $e): ?>
            <option value="<?= (int)$e['id_expediente'] ?>">
                <?= htmlspecialchars(($e['codigo_estudiante'] ?? '') . ' - ' . ($e['estudiante_nombre'] ?? 'Expediente ' . $e['id_expediente'])) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:18px;">
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Fecha Defensa *</label>
            <input type="date" name="fecha_defensa" required 
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Hora *</label>
            <input type="time" name="hora_defensa" required 
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Lugar / Aula *</label>
        <select name="lugar" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un aula o lugar...</option>
            <option value="Aula 101 - Edificio Principal">Aula 101 - Edificio Principal</option>
            <option value="Aula 102 - Edificio Principal">Aula 102 - Edificio Principal</option>
            <option value="Aula 201 - Edificio Académico">Aula 201 - Edificio Académico</option>
            <option value="Aula 202 - Edificio Académico">Aula 202 - Edificio Académico</option>
            <option value="Aula 301 - Sala de Grados">Aula 301 - Sala de Grados</option>
            <option value="Aula Magna">Aula Magna</option>
            <option value="Auditorio Principal">Auditorio Principal</option>
            <option value="Laboratorio de Computación">Laboratorio de Computación</option>
            <option value="Virtual - Plataforma">Virtual - Plataforma</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Estado</label>
        <select name="estado_defensa" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="programada" selected>Programada</option>
            <option value="confirmada">Confirmada</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
            <option value="aplazada">Aplazada</option>
        </select>
    </div>

    <div style="margin-bottom:20px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Observaciones</label>
        <textarea name="observaciones_programacion" rows="4" 
                  style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;"></textarea>
    </div>

    <div style="display:flex; gap:12px;">
        <a href="index.php?accion=mg_defensas_listar" 
           style="flex:1; padding:12px; text-align:center; background:#e0e0e0; color:#333; border-radius:6px; text-decoration:none; font-weight:bold;">← Volver</a>
        <button type="submit" 
                style="flex:1; padding:12px; background:#0066cc; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">💾 Guardar Defensa</button>
    </div>
</form>
<?php
$contenido = ob_get_clean();
// ✅ Ruta correcta
require_once __DIR__ . '/../config/plantilla.php';