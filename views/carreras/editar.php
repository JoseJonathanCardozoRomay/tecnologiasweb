<?php
$titulo_pagina = 'Editar Carrera';
ob_start();
?>

<h1>Editar Carrera</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=carrera_editar&id=<?= $carrera['id_carrera'] ?>">
    <label>Nombre de la Carrera:</label>
    <input type="text" name="nombre_carrera" value="<?= htmlspecialchars($carrera['nombre_carrera']) ?>" required>
    
    <button type="submit" class="btn btn-exito">Actualizar</button>
    <a href="index.php?accion=carreras_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';