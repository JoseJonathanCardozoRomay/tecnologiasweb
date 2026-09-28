<?php
/**
 * Crear Reunión de Seguimiento
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador','tutor']);
require_once __DIR__ . '/../config/conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Token inválido, recarga la página';
    } else {
        $fecha = trim($_POST['fecha'] ?? '');
        $hora = trim($_POST['hora'] ?? '');
        $lugar = trim($_POST['lugar'] ?? '');
        $asistentes = trim($_POST['asistentes'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        
        if (empty($fecha) || empty($hora)) {
            $error = 'Fecha y hora son obligatorias';
        } else {
            global $conexion;
            $stmt = $conexion->prepare("
                INSERT INTO reuniones_seguimiento 
                (fecha, hora, lugar, asistentes, descripcion, id_usuario_creo, creado_en)
                VALUES (:fecha, :hora, :lugar, :asistentes, :descripcion, :id_usuario, NOW())
            ");
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora', $hora);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':asistentes', $asistentes);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id_usuario', $_SESSION['id_usuario']);
            
            if ($stmt->execute()) {
                header('Location: index.php?accion=reuniones_listar');
                exit;
            } else {
                $error = 'Error al guardar la reunión';
            }
        }
    }
}

$titulo_pagina = 'Nueva Reunión';
ob_start();
?>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
    body {
        background: linear-gradient(rgba(0,38,77,0.85), rgba(0,38,77,0.85)),
                    url('https://www.unir.net/wp-content/uploads/2021/04/la-universidad-que-necesitamos_c-2-1.jpg') center/cover no-repeat fixed;
        min-height: 100vh;
        padding: 30px;
    }
    .contenedor {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    h1 { color: #003366; margin-bottom: 25px; }
    .alerta-error {
        background: #ffebee;
        color: #c62828;
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    label {
        display: block;
        margin: 15px 0 5px;
        font-weight: bold;
        color: #333;
    }
    input, textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
    }
    textarea { min-height: 100px; resize: vertical; }
    .botones { margin-top: 25px; display: flex; gap: 10px; }
    button, .btn {
        padding: 10px 25px;
        border-radius: 6px;
        border: none;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-guardar { background: #28a745; color: white; }
    .btn-volver { background: #6c757d; color: white; }
</style>

<div class="contenedor">
    <h1>📅 Nueva Reunión de Seguimiento</h1>

    <?php if (!empty($error)): ?>
        <div class="alerta-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?accion=reunion_crear">
        <input type="hidden" name="csrf_token" value="<?= csrf_generar() ?>">
        
        <label>Fecha:</label>
        <input type="date" name="fecha" required>
        
        <label>Hora:</label>
        <input type="time" name="hora" required>
        
        <label>Lugar / Enlace:</label>
        <input type="text" name="lugar" placeholder="Ej: Aula 302 o enlace de videollamada">
        
        <label>Asistentes:</label>
        <input type="text" name="asistentes" placeholder="Nombres de los participantes">
        
        <label>Descripción / Acuerdos:</label>
        <textarea name="descripcion" placeholder="Temas tratados, decisiones, compromisos..."></textarea>
        
        <div class="botones">
            <button type="submit" class="btn-guardar">✅ Guardar Reunión</button>
            <a href="index.php?accion=reuniones_listar" class="btn btn-volver">Volver</a>
        </div>
    </form>
</div>
<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../layout.php';