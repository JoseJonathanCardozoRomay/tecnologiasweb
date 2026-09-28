<?php
if (!isset($tutorias)) $tutorias = [];
if (!isset($error)) $error = '';

$titulo_pagina = 'Registrar Seguimiento';
ob_start();
?>
<h1>Registrar Seguimiento de Sesión</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=seguimiento_crear">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Tutoría *</label>
    <select name="id_tutoria" required>
        <option value="">Seleccione tutoría</option>
        <?php if (!empty($tutorias)): ?>
            <?php foreach ($tutorias as $t): ?>
            <option value="<?= (int)($t['id_tutoria'] ?? 0) ?>">
                <?= htmlspecialchars(($t['nombre_estudiante'] ?? '').' — '.($t['nombre_materia'] ?? '').' — '.($t['fecha'] ?? '')) ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <label>¿Asistió? *</label>
    <select name="asistio" required>
        <option value="">Seleccione</option>
        <option value="si">Sí</option>
        <option value="no">No</option>
    </select>
    
    <label>Temas Tratados *</label>
    <textarea name="temas_tratados" rows="4" placeholder="Describa los temas vistos en la sesión..." required></textarea>
    
    <label>Nivel de Avance</label>
    <select name="avance">
        <option value="sin_avance">Sin avance</option>
        <option value="parcial" selected>Avance parcial</option>
        <option value="logrado">Objetivo logrado</option>
    </select>
    
    <label>Recomendaciones</label>
    <textarea name="recomendaciones" rows="3" placeholder="Observaciones o recomendaciones..."></textarea>
    
    <button type="submit" class="btn btn-primario">Guardar Seguimiento</button>
    <a href="index.php?accion=seguimientos_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';