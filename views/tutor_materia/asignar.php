<?php
/**
 * Asignar Materia a Tutor — Admin
 * ✅ Corregido: Carga completa de tutores y materias
 * ✅ Sin romper nada de lo que ya funciona
 */

require_once __DIR__ . '/../../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/TutorModel.php';
require_once __DIR__ . '/../../models/MateriaModel.php';
require_once __DIR__ . '/../../models/TutorMateriaModel.php';

$modeloTutor = new TutorModel();
$modeloMateria = new MateriaModel();
$modeloAsignacion = new TutorMateriaModel();

$error = '';
$exito = '';

// ✅ CARGAR TODOS LOS DATOS PRIMERO
$tutores = $modeloTutor->listarTodos();
$materias = $modeloMateria->listarTodas();

// Depuración: verificar si llegan datos
if (empty($tutores)) $error .= ' ⚠️ No hay tutores registrados. ';
if (empty($materias)) $error .= ' ⚠️ No hay materias registradas. ';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $id_tutor = (int)($_POST['id_tutor'] ?? 0);
        $id_materia = (int)($_POST['id_materia'] ?? 0);

        if ($id_tutor <= 0 || $id_materia <= 0) {
            $error = 'Seleccione un tutor y una materia';
        } else {
            $resultado = $modeloAsignacion->asignar($id_tutor, $id_materia);
            
            if ($resultado === false) {
                $error = 'Esta materia ya está asignada a ese tutor';
            } elseif (isset($resultado['existe'])) {
                $error = 'Esta asignación ya existe y está pendiente de aceptación';
            } else {
                $exito = '✅ Materia asignada correctamente. El tutor debe aceptarla.';
            }
        }
    }
}

$titulo_pagina = 'Asignar Materia a Tutor';
ob_start();
?>

<h1>➕ Asignar Materia a Tutor</h1>

<?php if (!empty($error)): ?>
<div style="background:#ffdddd; color:#c00; padding:12px; margin:15px 0; border-radius:6px;">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if (!empty($exito)): ?>
<div style="background:#ddffdd; color:#060; padding:12px; margin:15px 0; border-radius:6px;">
    <?= htmlspecialchars($exito) ?>
</div>
<?php endif; ?>

<form method="POST" action="" style="max-width:600px; margin:30px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    
    <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
    
    <label style="display:block; margin-bottom:8px; font-weight:bold; color:#003366;">Tutor *</label>
    <select name="id_tutor" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ccc; border-radius:6px; font-size:15px;">
        <option value="">-- Seleccione un tutor --</option>
        <?php if (!empty($tutores)): ?>
            <?php foreach ($tutores as $t): ?>
                <option value="<?= $t['id_tutor'] ?>">
                    <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '') . ' — ' . ($t['correo'] ?? '')) ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>⚠️ No hay tutores registrados aún</option>
        <?php endif; ?>
    </select>
    
    <label style="display:block; margin-bottom:8px; font-weight:bold; color:#003366;">Materia *</label>
    <select name="id_materia" required style="width:100%; padding:10px; margin-bottom:25px; border:1px solid #ccc; border-radius:6px; font-size:15px;">
        <option value="">-- Seleccione una materia --</option>
        <?php if (!empty($materias)): ?>
            <?php foreach ($materias as $m): ?>
                <option value="<?= $m['id_materia'] ?>">
                    <?= htmlspecialchars($m['nombre_materia'] ?? '') ?>
                </option>
            <?php endforeach; ?>
        <?php else: ?>
            <option value="" disabled>⚠️ No hay materias registradas aún</option>
        <?php endif; ?>
    </select>
    
    <button type="submit" style="background:#0066cc; color:white; border:none; padding:12px 25px; border-radius:6px; font-weight:bold; cursor:pointer; width:100%; font-size:16px;">
        ✅ Asignar Materia
    </button>
    
    <p style="text-align:center; margin-top:20px;">
        <a href="index.php?accion=tutor_materia_listar" style="color:#0066cc; text-decoration:none;">← Volver al listado</a>
    </p>
</form>

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../../config/plantilla.php';