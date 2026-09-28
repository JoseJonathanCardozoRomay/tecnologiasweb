 <?php
$titulo_pagina = 'Crear Estudiante';
ob_start();
?>
<h1>Registrar Nuevo Estudiante</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=estudiante_crear">
    <!-- ✅ Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    
    <label>Apellido:</label>
    <input type="text" name="apellido" required>
    
    <label>Teléfono:</label>
    <input type="text" name="telefono">
    
    <label>Carrera:</label>
    <select name="id_carrera" required>
        <option value="">Seleccione carrera</option>
        <?php foreach ($carreras as $c): ?>
        <!-- ✅ ID protegido como número -->
        <option value="<?= (int)$c['id_carrera'] ?>">
            <?= htmlspecialchars($c['nombre_carrera']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <label>Semestre:</label>
    <input type="number" name="semestre" min="1" max="12" required>
    
    <label>Número de Registro Universitario:</label>
    <input type="text" name="registro_universitario" required>
    
    <button type="submit" class="btn btn-primario">Guardar Estudiante</button>
    <a href="index.php?accion=estudiantes_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';