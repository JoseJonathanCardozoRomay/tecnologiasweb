<?php
/**
 * Crear Informe de Avance
 * Tablas confirmadas: tutorias | informes_avance
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','estudiante']);
require_once __DIR__ . '/../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $id_tutoria = (int)($_POST['id_tutoria'] ?? 0);
        $titulo = trim($_POST['titulo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $progreso = (int)($_POST['progreso_porcentaje'] ?? 0);
        
        if ($id_tutoria <= 0) {
            $error = 'Debe seleccionar una tutoría';
        } elseif (empty($titulo)) {
            $error = 'El título es obligatorio';
        } elseif (empty($descripcion)) {
            $error = 'La descripción es obligatoria';
        } elseif ($progreso < 0 || $progreso > 100) {
            $error = 'El porcentaje debe estar entre 0 y 100';
        } else {
            global $conexion;
            $stmt = $conexion->prepare("
                INSERT INTO informes_avance 
                (id_tutoria, titulo, descripcion, progreso_porcentaje, id_usuario_crea, estado)
                VALUES (:id_tutoria, :titulo, :descripcion, :progreso, :id_usuario, 'borrador')
            ");
            $stmt->bindParam(':id_tutoria', $id_tutoria);
            $stmt->bindParam(':titulo', $titulo);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':progreso', $progreso);
            $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
            
            if ($stmt->execute()) {
                header('Location: index.php?accion=informes_listar');
                exit;
            } else {
                $error = 'Error al guardar el informe';
            }
        }
    }
}

global $conexion;
$tutorias = [];
$stmt = $conexion->query("SELECT id_tutoria FROM tutorias ORDER BY id_tutoria DESC");
if ($stmt) {
    $tutorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$titulo_pagina = 'Nuevo Informe de Avance';
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
    .contenedor {
        max-width: 500px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h1 { color: #003366; text-align: center; margin-bottom: 25px; font-size: 22px; }
    .alerta-error { background: #ffebee; color: #c62828; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
    label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; }
    select, input, textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }
    textarea { min-height: 100px; resize: vertical; }
    .btn-guardar {
        width: 100%;
        margin-top: 20px;
        padding: 12px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-volver {
        display: inline-block;
        margin-top: 15px;
        color: #6c757d;
        text-decoration: none;
    }
</style>

<div class="contenedor">
    <h1>📄 Nuevo Informe de Avance</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?accion=informe_crear">
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        
        <label>Tutoría *</label>
        <select name="id_tutoria" required>
            <option value="">Seleccione una tutoría</option>
            <?php if (!empty($tutorias)): ?>
                <?php foreach ($tutorias as $t): ?>
                    <option value="<?= $t['id_tutoria'] ?>">
                        Tutoría #<?= $t['id_tutoria'] ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>⚠️ Primero cree una tutoría</option>
            <?php endif; ?>
        </select>
        
        <label>Título del Informe *</label>
        <input type="text" name="titulo" required>
        
        <label>Descripción y Detalles *</label>
        <textarea name="descripcion" required></textarea>
        
        <label>Porcentaje de Avance (0-100) *</label>
        <input type="number" name="progreso_porcentaje" min="0" max="100" required>
        
        <button type="submit" class="btn-guardar">✅ Crear Informe</button>
        <a href="index.php?accion=informes_listar" class="btn-volver">← Volver</a>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';