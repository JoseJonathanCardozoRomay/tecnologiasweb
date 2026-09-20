<?php
/**
 * tutor_foto_subir.php — Endpoint para subir fotos de perfil de tutores
 * 
 * Sube imágenes como archivos físicos y retorna la ruta relativa.
 * No guarda data URIs en la base de datos.
 */
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'tutor']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutorModel.php';

header('Content-Type: application/json');

$response = ['ok' => false, 'error' => '', 'ruta' => ''];

try {
    // Verificar que sea POST y tenga archivo
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }
    
    if (!isset($_FILES['foto_file']) || $_FILES['foto_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se recibió ningún archivo válido');
    }
    
    $file = $_FILES['foto_file'];
    
    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Solo se permiten imágenes JPEG, PNG o WebP');
    }
    
    // Validar tamaño (máximo 2MB)
    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        throw new Exception('La imagen no debe superar los 2MB');
    }
    
    // Obtener ID del tutor
    $idUsuario = $_SESSION['id_usuario'] ?? 0;
    $rolSesion = $_SESSION['rol'] ?? '';
    
    $tutorModel = new TutorModel($pdo);
    $tutor = $tutorModel->obtenerPorUsuario($idUsuario);
    
    if (!$tutor) {
        throw new Exception('No se encontró el registro del tutor');
    }
    
    $idTutor = $tutor['id_tutor'];
    
    // Crear directorio si no existe
    $uploadDir = __DIR__ . '/../assets/img/tutores/';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            throw new Exception('No se pudo crear el directorio de uploads');
        }
    }
    
    // Generar nombre de archivo único
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nombreArchivo = 'foto_' . $idTutor . '_' . time() . '.' . $extension;
    $rutaCompleta = $uploadDir . $nombreArchivo;
    
    // Eliminar foto anterior si existe
    if (!empty($tutor['foto_perfil'])) {
        $rutaAnterior = __DIR__ . '/../' . $tutor['foto_perfil'];
        if (file_exists($rutaAnterior) && strpos($rutaAnterior, 'assets/img/tutores/') !== false) {
            @unlink($rutaAnterior);
        }
    }
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
        throw new Exception('Error al guardar el archivo');
    }
    
    // Generar ruta relativa para la BD
    $rutaRelativa = 'assets/img/tutores/' . $nombreArchivo;
    
    $response = [
        'ok' => true,
        'ruta' => $rutaRelativa
    ];
    
} catch (Exception $e) {
    $response = [
        'ok' => false,
        'error' => $e->getMessage()
    ];
}

echo json_encode($response);
