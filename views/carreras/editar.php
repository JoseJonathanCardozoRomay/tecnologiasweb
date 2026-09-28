 <?php
$titulo_pagina = 'Editar Carrera';
ob_start();
?>
<h1>Editar Carrera</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=carrera_editar&id=<?= (int)$carrera['id_carrera'] ?>">
    <!-- ✅ Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Nombre de la Carrera:</label>
    <!-- ✅ Ya estaba protegido ✅ -->
    <input type="text" name="nombre_carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>" required>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=carreras_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';