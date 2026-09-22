<?php
$titulo_pagina = 'Crear Rol';
ob_start();
?>

<h1>Crear Nuevo Rol</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=rol_crear">
    <label>Nombre del Rol:</label>
    <input type="text" name="nombre_rol" placeholder="ej: administrador, tutor, estudiante" required>
    
    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';