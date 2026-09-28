<?php
if (!isset($tutorias)) $tutorias = [];
if (!isset($registro)) $registro = [];
if (!isset($error)) $error = '';

$titulo_pagina = 'Editar Seguimiento de Sesión';
ob_start();
?>
<h1>Editar Seguimiento de Sesión</h1>

<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<form method="POST" action="index.php?accion=seguimiento_editar&id=<?= (int)($registro['id_seguimiento'] ?? 0) ?>">
    <!-- ✅ Token CSRF OBLIGATORIO -->
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label>Tutoría *</label>
    <select name="id_tutoria" required>
        <option value="">Seleccione tutoría</option>
        <?php if (!empty($tutorias)): ?>
            <?php foreach ($tutorias as $t): ?>
            <option value="<?= (int)($t['id_tutoria'] ?? 0) ?>" 
                <?= ((int)($t['id_tutoria'] ?? 0) === (int)($registro['id_tutoria'] ?? 0)) ? 'selected' : '' ?>>
                <?= htmlspecialchars(($t['nombre_estudiante'] ?? '').' — '.($t['nombre_materia'] ?? '').' — '.($t['fecha'] ?? '')) ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <label>¿Asistió? *</label>
    <select name="asistio" required>
        <option value="">Seleccione</option>
        <option value="si" <?= (($registro['asistio'] ?? '') === 'si') ? 'selected' : '' ?>>Sí</option>
        <option value="no" <?= (($registro['asistio'] ?? '') === 'no') ? 'selected' : '' ?>>No</option>
    </select>
    
    <label>Temas Tratados *</label>
    <textarea name="temas_tratados" rows="4" required><?= htmlspecialchars($registro['temas_tratados'] ?? '') ?></textarea>
    
    <label>Nivel de Avance</label>
    <select name="avance">
        <option value="sin_avance" <?= (($registro['avance'] ?? '') === 'sin_avance') ? 'selected' : '' ?>>Sin avance</option>
        <option value="parcial" <?= (($registro['avance'] ?? '') === 'parcial') ? 'selected' : '' ?>>Avance parcial</option>
        <option value="logrado" <?= (($registro['avance'] ?? '') === 'logrado') ? 'selected' : '' ?>>Objetivo logrado</option>
    </select>
    
    <label>Recomendaciones</label>
    <textarea name="recomendaciones" rows="3"><?= htmlspecialchars($registro['recomendaciones'] ?? '') ?></textarea>
    
    <button type="submit" class="btn btn-primario">Guardar Cambios</button>
    <a href="index.php?accion=seguimientos_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';