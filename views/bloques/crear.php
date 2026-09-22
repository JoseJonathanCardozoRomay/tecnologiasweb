<?php
$titulo_pagina = 'Crear Bloque Horario';
ob_start();
?>

<h1>Crear Nuevo Bloque Horario</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=bloque_crear">
    <label>Nombre del Bloque:</label>
    <input type="text" name="nombre_bloque" placeholder="Ej: Mañana, Tarde, Noche" required>

    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" required>

    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" required>

    <label>Descripción:</label>
    <textarea name="descripcion" rows="3" placeholder="Breve descripción opcional"></textarea>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=bloques_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';