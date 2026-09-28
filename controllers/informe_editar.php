<?php
/**
 * Editar Informe de Avance
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador', 'estudiante']);
require_once __DIR__ . '/../config/conexion.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) {
    header('Location: index.php?accion=informes_listar');
    exit;
}

global $conexion;

// Obtener informe
$stmt = $conexion->prepare("SELECT * FROM informes_avance WHERE id_informe = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$informe = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$informe) {
    header('Location: index.php?accion=informes_listar');
    exit;
}

// Verificar permiso
$rol_actual = $_SESSION['rol_nombre'] ?? '';
if ($rol_actual === 'estudiante' && (int)($informe['id_usuario_crea'] ?? 0) !== (int)($_SESSION['id_usuario'] ?? 0)) {
    echo "<script>alert('No tienes permiso para editar este informe'); window.location='index.php?accion=informes_listar';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $progreso = (int)($_POST['progreso_porcentaje'] ?? 0);
        
        if (empty($titulo)) {
            $error = 'El título es obligatorio';
        } elseif (empty($descripcion)) {
            $error = 'La descripción es obligatoria';
        } elseif ($progreso < 0 || $progreso > 100) {
            $error = 'El porcentaje debe estar entre 0 y 100';
        } else {
            $stmt = $conexion->prepare("
                UPDATE informes_avance 
                SET titulo = :titulo, descripcion = :descripcion, progreso_porcentaje = :progreso
                WHERE id_informe = :id
            ");
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':progreso', $progreso);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                header('Location: index.php?accion=informes_listar');
                exit;
            } else {
                $error = 'Error al actualizar el informe';
            }
        }
    }
}

$titulo_pagina = 'Editar Informe de Avance';
ob_start();
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
    body {
        background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                    url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
        min-height: 100vh;
        padding: 30px;
    }
    .contenedor { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    h1 { color: #003366; text-align: center; margin-bottom: 25px; font-size: 22px; }
    .alerta-error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; }
    input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    textarea { min-height: 100px; resize: vertical; }
    .btn-guardar { width: 100%; margin-top: 20px; padding: 12px; background: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; }
    .btn-volver { display: inline-block; margin-top: 15px; color: #6c757d; text-decoration: none; }
</style>

<div class="contenedor">
    <h1>✏️ Editar Informe de Avance</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?accion=informe_editar&id=<?= $id ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        
        <label>Tutoría</label>
        <input type="text" value="Tutoría #<?= $informe['id_tutoria'] ?>" disabled style="background:#f5f5f5;">
        
        <label>Título del Informe *</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($informe['titulo']) ?>" required>
        
        <label>Descripción y Detalles *</label>
        <textarea name="descripcion" required><?= htmlspecialchars($informe['descripcion']) ?></textarea>
        
        <label>Porcentaje de Avance (0-100) *</label>
        <input type="number" name="progreso_porcentaje" min="0" max="100" value="<?= $informe['progreso_porcentaje'] ?>" required>
        
        <button type="submit" class="btn-guardar">💾 Actualizar</button>
        <a href="index.php?accion=informes_listar" class="btn-volver">← Volver</a>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';