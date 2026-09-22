<?php
$titulo_pagina = 'Crear Nuevo Estudiante';
ob_start();
?>

<h1>Crear Nuevo Estudiante</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#cc0000; padding:10px; margin:10px 0; border-radius:4px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=estudiante_crear" style="max-width:500px; margin:20px auto;">

    <div style="margin-bottom:15px;">
        <label>Nombre:</label>
        <input type="text" name="nombre" required style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Apellido:</label>
        <input type="text" name="apellido" required style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Teléfono:</label>
        <input type="text" name="telefono" style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Carrera:</label>
        <select name="id_carrera" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">Seleccione una carrera</option>
            <?php if (!empty($carreras)): ?>
                <?php foreach ($carreras as $c): ?>
                <option value="<?= $c['id_carrera'] ?>">
                    <?= htmlspecialchars($c['nombre_carrera']) ?>
                </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div style="margin-bottom:15px;">
        <label>Semestre:</label>
        <input type="number" name="semestre" min="1" max="12" required style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <div style="margin-bottom:15px;">
        <label>Número de Registro Universitario:</label>
        <input type="text" name="registro_universitario" style="width:100%; padding:8px; margin-top:5px;">
    </div>

    <button type="submit" style="background:#003366; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:16px;">Guardar</button>
    <a href="index.php?accion=estudiantes_listar" style="margin-left:10px; color:#666;">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';