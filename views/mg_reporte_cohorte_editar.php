<?php
if (!isset($error)) $error = '';
if (!isset($reporte)) $reporte = [];
$titulo_pagina = 'Editar Cohorte';
ob_start();
?>
<h1>Editar Cohorte</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:12px; margin:15px 0; border-radius:6px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:650px; margin:25px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
    
    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Nombre de la Cohorte *</label>
        <input type="text" name="nombre_cohorte" required value="<?= htmlspecialchars($reporte['nombre_cohorte'] ?? '') ?>" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:18px;">
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Año de Inicio *</label>
            <input type="number" name="anio_inicio" required value="<?= $reporte['anio_inicio'] ?? '' ?>" min="2000" max="2030" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
        <div>
            <label style="display:block; font-weight:bold; margin-bottom:6px;">Año de Fin</label>
            <input type="number" name="anio_fin" value="<?= $reporte['anio_fin'] ?? '' ?>" min="2000" max="2030" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
        </div>
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Total Estudiantes</label>
        <input type="number" name="total_estudiantes" value="<?= $reporte['total_estudiantes'] ?? 0 ?>" min="0" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="margin-bottom:18px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Estado</label>
        <select name="estado" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
            <option value="activa" <?= ($reporte['estado'] ?? '') == 'activa' ? 'selected' : '' ?>>Activa</option>
            <option value="inactiva" <?= ($reporte['estado'] ?? '') == 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
            <option value="completada" <?= ($reporte['estado'] ?? '') == 'completada' ? 'selected' : '' ?>>Completada</option>
        </select>
    </div>

    <div style="margin-bottom:20px;">
        <label style="display:block; font-weight:bold; margin-bottom:6px;">Descripción</label>
        <textarea name="descripcion" rows="4" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;"><?= htmlspecialchars($reporte['descripcion'] ?? '') ?></textarea>
    </div>

    <div style="display:flex; gap:12px;">
        <a href="index.php?accion=mg_reporte_cohorte_listar" style="flex:1; padding:12px; text-align:center; background:#e0e0e0; color:#333; border-radius:6px; text-decoration:none; font-weight:bold;">← Volver</a>
        <button type="submit" style="flex:1; padding:12px; background:#0066cc; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">💾 Guardar Cambios</button>
    </div>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';