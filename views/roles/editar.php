 <?php
$titulo_pagina = 'Editar Rol';
ob_start();
?>
<h1>Editar Rol</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <!-- ✅ Protegido -->
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=rol_editar&id=<?= (int)$rol['id_rol'] ?>">
    <!-- ✅ Token CSRF -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Nombre del Rol:</label>
    <!-- ✅ Ya estaba protegido ✅ -->
    <input type="text" name="nombre_rol" value="<?= htmlspecialchars($rol['nombre_rol']) ?>" required>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';