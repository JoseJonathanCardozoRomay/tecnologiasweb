<?php
$titulo_pagina = 'Registrar Seguimiento';
ob_start();
?>

<h1>Registrar Seguimiento</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="index.php?accion=seguimiento_crear">
    <label>Tutoría:</label>
    <select name="id_tutoria" required>
        <option value="">Seleccione</option>
        <?php foreach ($tutorias as $t): ?>
        <option value="<?= $t['id_tutoria'] ?>">Tutoría #<?= $t['id_tutoria'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>¿Asistió?</label>
    <select name="asistio" required>
        <option value="">Seleccione</option>
        <option value="si">Sí ✅</option>
        <option value="no">No ❌</option>
    </select>

    <label>Temas Tratados:</label>
    <textarea name="temas_tratados" rows="4" placeholder="Qué temas se revisaron en la sesión..."></textarea>

    <label>Nivel de Avance:</label>
    <select name="avance">
        <option value="sin_avance">Sin avance</option>
        <option value="parcial">Avance Parcial</option>
        <option value="logrado">Objetivo Logrado</option>
    </select>

    <label>Recomendaciones:</label>
    <textarea name="recomendaciones" rows="3" placeholder="Sugerencias para próximas sesiones..."></textarea>

    <button type="submit" class="btn btn-primario">Guardar</button>
    <a href="index.php?accion=seguimientos_listar" class="btn btn-volver">Volver</a>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';