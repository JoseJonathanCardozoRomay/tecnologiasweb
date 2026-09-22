<?php
$titulo_pagina = 'Crear Carrera';
ob_start();
?>

<h1>Crear Nueva Carrera</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=carrera_crear">
    <label>Nombre de la Carrera:</label>
    <input type="text" name="nombre_carrera" required>
    
    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=carreras_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';