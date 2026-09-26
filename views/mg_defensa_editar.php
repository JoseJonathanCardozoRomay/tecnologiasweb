if (!isset($error)) $error = '';
if (!isset($defensa)) $defensa = [];
if (!isset($tribunales)) $tribunales = [];
if (!isset($expedientes)) $expedientes = [];

$titulo_pagina = 'Editar Defensa Programada';
ob_start();
?>
<h1>Editar Defensa</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:12px; margin:15px 0; border-radius:6px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:650px; margin:25px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    
    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Tribunal / Jurado *</label>
        <select name="id_tribunal" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un tribunal...</option>
            <?php foreach ($tribunales as $t): ?>
            <option value="<?= $t['id_tribunal'] ?>" <?= ($defensa['id_tribunal'] ?? 0) == $t['id_tribunal'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['nombre_completo']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Expediente / Estudiante *</label>
        <select name="id_expediente" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un expediente...</option>
            <?php foreach ($expedientes as $e): ?>
            <option value="<?= $e['id_expediente'] ?>" <?= ($defensa['id_expediente'] ?? 0) == $e['id_expediente'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['nombre_estudiante'] ?? $e['titulo'] ?? 'Expediente '.$e['id_expediente']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:18px;">
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Fecha Defensa *</label>
            <input type="date" name="fecha_defensa" required 
                   value="<?= htmlspecialchars($defensa['fecha_defensa'] ?? '') ?>" 
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Hora *</label>
            <input type="time" name="hora_defensa" required 
                   value="<?= htmlspecialchars($defensa['hora_defensa'] ?? '') ?>" 
                   style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Lugar / Aula *</label>
        <select name="lugar" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione un aula o lugar...</option>
            <option value="Aula 101 - Edificio Principal" <?= ($defensa['lugar'] ?? '') == 'Aula 101 - Edificio Principal' ? 'selected' : '' ?>>Aula 101 - Edificio Principal</option>
            <option value="Aula 102 - Edificio Principal" <?= ($defensa['lugar'] ?? '') == 'Aula 102 - Edificio Principal' ? 'selected' : '' ?>>Aula 102 - Edificio Principal</option>
            <option value="Aula 201 - Edificio Académico" <?= ($defensa['lugar'] ?? '') == 'Aula 201 - Edificio Académico' ? 'selected' : '' ?>>Aula 201 - Edificio Académico</option>
            <option value="Aula 202 - Edificio Académico" <?= ($defensa['lugar'] ?? '') == 'Aula 202 - Edificio Académico' ? 'selected' : '' ?>>Aula 202 - Edificio Académico</option>
            <option value="Aula 301 - Sala de Grados" <?= ($defensa['lugar'] ?? '') == 'Aula 301 - Sala de Grados' ? 'selected' : '' ?>>Aula 301 - Sala de Grados</option>
            <option value="Aula Magna" <?= ($defensa['lugar'] ?? '') == 'Aula Magna' ? 'selected' : '' ?>>Aula Magna</option>
            <option value="Auditorio Principal" <?= ($defensa['lugar'] ?? '') == 'Auditorio Principal' ? 'selected' : '' ?>>Auditorio Principal</option>
            <option value="Laboratorio de Computación" <?= ($defensa['lugar'] ?? '') == 'Laboratorio de Computación' ? 'selected' : '' ?>>Laboratorio de Computación</option>
            <option value="Virtual - Plataforma" <?= ($defensa['lugar'] ?? '') == 'Virtual - Plataforma' ? 'selected' : '' ?>>Virtual - Plataforma</option>
        </select>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Estado</label>
        <select name="estado_defensa" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="programada" <?= ($defensa['estado_defensa'] ?? '') == 'programada' ? 'selected' : '' ?>>Programada</option>
            <option value="realizada" <?= ($defensa['estado_defensa'] ?? '') == 'realizada' ? 'selected' : '' ?>>Realizada</option>
            <option value="cancelada" <?= ($defensa['estado_defensa'] ?? '') == 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
            <option value="aplazada" <?= ($defensa['estado_defensa'] ?? '') == 'aplazada' ? 'selected' : '' ?>>Aplazada</option>
        </select>
    </div>

    <div style="margin-bottom:20px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Observaciones</label>
        <textarea name="observaciones_programacion" rows="4" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;"><?= htmlspecialchars($defensa['observaciones_programacion'] ?? '') ?></textarea>
    </div>

    <div style="display:flex; gap:12px;">
        <a href="index.php?accion=mg_defensas_listar" style="flex:1; padding:12px; text-align:center; background:#e0e0e0; color:#333; border-radius:6px; text-decoration:none; font-weight:bold;">← Volver</a>
        <button type="submit" style="flex:1; padding:12px; background:#0066cc; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">💾 Guardar Cambios</button>
    </div>
</form>
<?php
$contenido = ob_get_clean();
// ✅ RUTA CORRECTA
require_once __DIR__ . '/../layout.php';