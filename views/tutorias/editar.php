 <?php
if (!isset($estudiantes)) $estudiantes = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
if (!isset($datos_estudiante)) $datos_estudiante = [];
if (!isset($tutoria)) $tutoria = [];
if (!isset($error)) $error = '';

$titulo_pagina = 'Editar Tutoría';
ob_start();
?>
<h1>Editar Tutoría</h1>
<?php if (!empty($error)): ?>
<div class="alerta alerta-error">
    <?= htmlspecialchars($error ?? '') ?>
</div>
<?php endif; ?>
<form method="POST" action="index.php?accion=tutoria_editar&id=<?= (int)($tutoria['id_tutoria'] ?? 0) ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <!-- ESTUDIANTE: Admin selecciona / Tutor y Estudiante ven en solo lectura -->
    <?php if ($rol_actual === 'administrador'): ?>
    <label>Estudiante:</label>
    <select name="id_estudiante" required>
        <option value="">Seleccione estudiante</option>
        <?php if (!empty($estudiantes)): ?>
            <?php foreach ($estudiantes as $e): ?>
            <option value="<?= (int)$e['id_estudiante'] ?>"
                <?= ((int)($e['id_estudiante'] ?? 0) === (int)($tutoria['id_estudiante'] ?? 0)) ? 'selected' : '' ?>>
                <?= htmlspecialchars(($e['nombre'] ?? '') . ' ' . ($e['apellido'] ?? '')) ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    <?php else: ?>
    <label>Estudiante:</label>
    <input type="text" 
           value="<?= htmlspecialchars(($datos_estudiante['nombre'] ?? '') . ' ' . ($datos_estudiante['apellido'] ?? '')) ?>" 
           readonly style="background:#e9ecef; border:none; padding:8px; width:100%;">
    <input type="hidden" name="id_estudiante" value="<?= (int)($tutoria['id_estudiante'] ?? 0) ?>">
    <?php endif; ?>
    
    <label>Tutor:</label>
    <select name="id_tutor" required>
        <option value="">Seleccione tutor</option>
        <?php if (!empty($tutores)): ?>
            <?php foreach ($tutores as $t): ?>
            <option value="<?= (int)$t['id_tutor'] ?>"
                <?= ((int)($t['id_tutor'] ?? 0) === (int)($tutoria['id_tutor'] ?? 0)) ? 'selected' : '' ?>>
                <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <label>Materia:</label>
    <select name="id_materia" required>
        <option value="">Seleccione materia</option>
        <?php if (!empty($materias)): ?>
            <?php foreach ($materias as $m): ?>
            <option value="<?= (int)$m['id_materia'] ?>"
                <?= ((int)($m['id_materia'] ?? 0) === (int)($tutoria['id_materia'] ?? 0)) ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['nombre_materia'] ?? '') ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
    
    <label>Fecha:</label>
    <input type="date" name="fecha" value="<?= htmlspecialchars($tutoria['fecha'] ?? '') ?>" required>
    
    <label>Hora de Inicio:</label>
    <input type="time" name="hora_inicio" value="<?= htmlspecialchars($tutoria['hora_inicio'] ?? '') ?>" required>
    
    <label>Hora de Fin:</label>
    <input type="time" name="hora_fin" value="<?= htmlspecialchars($tutoria['hora_fin'] ?? '') ?>" required>
    
    <label>Modalidad:</label>
    <select name="modalidad" required>
        <option value="presencial" <?= ($tutoria['modalidad'] ?? '') === 'presencial' ? 'selected' : '' ?>>Presencial</option>
        <option value="virtual" <?= ($tutoria['modalidad'] ?? '') === 'virtual' ? 'selected' : '' ?>>Virtual</option>
    </select>
    
    <label>Lugar o Enlace:</label>
    <input type="text" name="lugar_o_enlace" value="<?= htmlspecialchars($tutoria['lugar_o_enlace'] ?? '') ?>">
    
    <label>Estado:</label>
    <select name="estado" required>
        <option value="pendiente" <?= ($tutoria['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
        <option value="confirmada" <?= ($tutoria['estado'] ?? '') === 'confirmada' ? 'selected' : '' ?>>Confirmada</option>
        <option value="realizada" <?= ($tutoria['estado'] ?? '') === 'realizada' ? 'selected' : '' ?>>Realizada</option>
        <option value="cancelada" <?= ($tutoria['estado'] ?? '') === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
    </select>
    
    <label>Observaciones:</label>
    <textarea name="observaciones" rows="3"><?= htmlspecialchars($tutoria['observaciones'] ?? '') ?></textarea>
    
    <button type="submit" class="btn btn-exito">Actualizar Tutoría</button>
    <a href="index.php?accion=tutorias_listar" class="btn btn-volver">Volver</a>
</form>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';