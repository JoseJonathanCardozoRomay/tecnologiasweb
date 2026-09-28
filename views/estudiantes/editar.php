<?php
$titulo_pagina = 'Editar Estudiante';
ob_start();
?>
<h1>Editar Estudiante</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:12px; margin-bottom:20px; border-radius:6px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if (!empty($exito)): ?>
<div style="background:#ddffdd; color:#060; padding:12px; margin-bottom:20px; border-radius:6px;">
    <?= htmlspecialchars($exito) ?>
</div>
<?php endif; ?>

<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <div style="margin-bottom:15px;">
        <label>Nombre: *</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($estudiante['nombre']) ?>" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Apellido: *</label>
        <input type="text" name="apellido" value="<?= htmlspecialchars($estudiante['apellido']) ?>" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($estudiante['telefono'] ?? '') ?>" style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Carrera: *</label>
        <select name="id_carrera" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
            <option value="">Seleccione carrera</option>
            <?php foreach ($carreras as $c): ?>
            <option value="<?= $c['id_carrera'] ?>" <?= ($estudiante['id_carrera'] == $c['id_carrera']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['nombre_carrera']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Semestre: *</label>
        <input type="number" name="semestre" value="<?= htmlspecialchars($estudiante['semestre']) ?>" min="1" max="12" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <div style="margin-bottom:20px;">
        <label>Número de Registro Universitario: *</label>
        <input type="text" name="registro_universitario" value="<?= htmlspecialchars($estudiante['registro_universitario']) ?>" required style="width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:6px;">
    </div>

    <button type="submit" style="background:#0066cc; color:white; padding:12px 25px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">Guardar Cambios</button>
    <a href="index.php?accion=estudiantes_listar" style="display:inline-block; margin-left:10px; padding:12px 25px; background:#6c757d; color:white; text-decoration:none; border-radius:6px; font-weight:bold;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';