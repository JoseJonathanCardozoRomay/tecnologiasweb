 <?php
$titulo_pagina = 'Editar Materia';
ob_start();
?>
<h1>Editar Materia</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=materia_editar&id=<?= (int)$materia['id_materia'] ?>">
    <!-- ✅ Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Nombre de la Materia:</label>
    <!-- ✅ Ya estaba protegido ✅ -->
    <input type="text" name="nombre_materia" value="<?= htmlspecialchars($materia['nombre_materia']) ?>" required>
    
    <label>Carrera:</label>
    <select name="id_carrera">
        <option value="">General / Sin carrera</option>
        <?php foreach ($carreras as $c): ?>
        <!-- ✅ ID protegido como número -->
        <option value="<?= (int)$c['id_carrera'] ?>" 
            <?= $c['id_carrera'] == $materia['id_carrera'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['nombre_carrera']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=materias_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';