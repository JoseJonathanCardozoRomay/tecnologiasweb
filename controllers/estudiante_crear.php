<?php
/**
 * Crear Estudiante — Sincronizado con plantilla + CSRF
 * Crea automáticamente usuario + registro de estudiante
 */
require_once __DIR__ . '/../config/sesion.php';
require_once __DIR__ . '/../config/conexion.php';

// Solo administrador
if (!tieneRol(['administrador'])) {
    echo "<script>alert('No tienes permiso para acceder a esta sección');history.back();</script>";
    exit;
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Verificar token CSRF
    if (!isset($_POST['csrf_token']) || !csrf_validar($_POST['csrf_token'])) {
        $error = 'Sesión inválida. Intenta nuevamente.';
    } else {
        // Recibir datos
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $id_carrera = (int)($_POST['id_carrera'] ?? 0);
        $semestre = (int)($_POST['semestre'] ?? 0);
        $registro_universitario = trim($_POST['registro_universitario'] ?? '');

        // Validar campos obligatorios
        if (empty($nombre) || empty($apellido) || $id_carrera <= 0 || $semestre <= 0 || empty($registro_universitario)) {
            $error = 'Completa todos los campos con *';
        } else {
            try {
                $conexion->beginTransaction();

                // 1. Crear usuario automáticamente (rol = estudiante = 3)
                $usuario = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $nombre)) . '.' . trim(preg_replace('/[^a-zA-Z0-9]/', '', $apellido)));
                $correo = $usuario . '@upds.edu.bo';
                $contrasena = password_hash('123456', PASSWORD_DEFAULT);

                $stmt = $conexion->prepare("INSERT INTO usuarios 
                    (id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado)
                    VALUES (3, :nombre, :apellido, :correo, :usuario, :pass, :telefono, 'activo')");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':correo' => $correo,
                    ':usuario' => $usuario,
                    ':pass' => $contrasena,
                    ':telefono' => $telefono
                ]);

                $id_usuario = $conexion->lastInsertId();

                // 2. Crear registro de estudiante
                $stmt = $conexion->prepare("INSERT INTO estudiantes 
                    (id_usuario, id_carrera, semestre, registro_universitario)
                    VALUES (:id_usuario, :id_carrera, :semestre, :registro)");
                $stmt->execute([
                    ':id_usuario' => $id_usuario,
                    ':id_carrera' => $id_carrera,
                    ':semestre' => $semestre,
                    ':registro' => $registro_universitario
                ]);

                $conexion->commit();
                $exito = "✅ Estudiante registrado correctamente.\nUsuario: $usuario\nContraseña: 123456";

            } catch (PDOException $e) {
                $conexion->rollBack();
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $error = '❌ El número de registro o el usuario ya existe en el sistema';
                } else {
                    $error = '❌ Error al guardar: ' . $e->getMessage();
                }
            }
        }
    }
}

// Obtener carreras para el select
$stmt = $conexion->query("SELECT * FROM carreras ORDER BY nombre_carrera");
$carreras = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/estudiantes/crear.php';