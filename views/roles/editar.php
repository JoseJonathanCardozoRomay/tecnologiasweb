<?php
$titulo_pagina = 'Editar Rol';
ob_start();
?>

<h1>Editar Rol</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=rol_editar&id=<?= $rol['id_rol'] ?>">
    <label>Nombre del Rol:</label>
    <input type="text" name="nombre_rol" value="<?= htmlspecialchars($rol['nombre_rol']) ?>" required>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';