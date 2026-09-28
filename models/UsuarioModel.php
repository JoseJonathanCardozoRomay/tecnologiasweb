<?php
/**
 * Modelo Usuario — Completo y Corregido
 * Crear ✅ Editar ✅ Eliminar ✅ Listar ✅ Paginación ✅ listarPorRol ✅ getConexion ✅
 */
require_once __DIR__ . '/../config/conexion.php';
class UsuarioModel {
    private $conexion;
    private $tabla = 'usuarios';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Listar todos los usuarios sin límite
     */
    public function listarTodos() {
        $consulta = "SELECT u.*, r.nombre_rol 
                     FROM {$this->tabla} u 
                     LEFT JOIN roles r ON u.id_rol = r.id_rol
                     ORDER BY u.id_usuario DESC";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar con paginación — para 500+ usuarios
     */
    public function listarPaginado($limite, $offset) {
        $consulta = "SELECT u.*, r.nombre_rol 
                     FROM {$this->tabla} u 
                     LEFT JOIN roles r ON u.id_rol = r.id_rol
                     ORDER BY u.id_usuario DESC
                     LIMIT :limite OFFSET :offset";
        
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener conexión — corrige error de propiedad privada
     */
    public function getConexion() {
        return $this->conexion;
    }

    /**
     * Listar roles del sistema
     */
    public function listarRoles() {
        $consulta = "SELECT * FROM roles ORDER BY nombre_rol ASC";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar usuarios por nombre de rol
     */
    public function listarPorRol($nombre_rol) {
        $nombre_rol = trim($nombre_rol);
        $sql = "SELECT u.* FROM {$this->tabla} u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE r.nombre_rol = :nombre_rol
                ORDER BY u.nombre ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_rol', $nombre_rol);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si nombre de usuario ya existe
     */
    public function existeUsuario($usuario, $ignorar_id = 0) {
        $usuario = trim($usuario);
        $ignorar_id = (int)$ignorar_id;
        if ($ignorar_id > 0) {
            $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE usuario = :usuario AND id_usuario != :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $ignorar_id);
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE usuario = :usuario";
            $stmt = $this->conexion->prepare($sql);
        }
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Verificar si correo ya existe
     */
    public function existeCorreo($correo, $ignorar_id = 0) {
        $correo = trim($correo);
        $ignorar_id = (int)$ignorar_id;
        if ($ignorar_id > 0) {
            $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE correo = :correo AND id_usuario != :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $ignorar_id);
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->tabla} WHERE correo = :correo";
            $stmt = $this->conexion->prepare($sql);
        }
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        $id_rol = (int)($datos['id_rol'] ?? 0);
        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $usuario = trim($datos['usuario'] ?? '');
        $contrasena = $datos['contrasena'] ?? '';
        $telefono = trim($datos['telefono'] ?? '');

        if ($id_rol <= 0 || empty($nombre) || empty($apellido) || empty($correo) || empty($usuario) || empty($contrasena)) {
            return false;
        }
        if ($this->existeUsuario($usuario) || $this->existeCorreo($correo)) {
            return false;
        }

        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->tabla} (id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado)
                VALUES (:id_rol, :nombre, :apellido, :correo, :usuario, :contrasena, :telefono, 'activo')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_rol', $id_rol);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':contrasena', $contrasena_hash);
        $stmt->bindParam(':telefono', $telefono);
        return $stmt->execute();
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id_usuario) {
        $id_usuario = (int)$id_usuario;
        $consulta = "SELECT * FROM {$this->tabla} WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id', $id_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id_usuario, $datos) {
        $id = (int)$id_usuario;
        $id_rol = (int)($datos['id_rol'] ?? 0);
        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $usuario = trim($datos['usuario'] ?? '');
        $telefono = trim($datos['telefono'] ?? '');
        $estado = trim($datos['estado'] ?? 'activo');

        if ($id_rol <= 0 || empty($nombre) || empty($apellido) || empty($correo) || empty($usuario)) {
            return false;
        }
        if ($this->existeUsuario($usuario, $id) || $this->existeCorreo($correo, $id)) {
            return false;
        }

        try {
            if (!empty($datos['contrasena'])) {
                $hash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
                $sql = "UPDATE {$this->tabla} SET id_rol=:id_rol, nombre=:nombre, apellido=:apellido, correo=:correo, 
                        usuario=:usuario, telefono=:telefono, estado=:estado, contrasena_hash=:contrasena WHERE id_usuario=:id";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':contrasena', $hash);
            } else {
                $sql = "UPDATE {$this->tabla} SET id_rol=:id_rol, nombre=:nombre, apellido=:apellido, correo=:correo, 
                        usuario=:usuario, telefono=:telefono, estado=:estado WHERE id_usuario=:id";
                $stmt = $this->conexion->prepare($sql);
            }
            $stmt->bindParam(':id_rol', $id_rol);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido', $apellido);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error actualizar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Eliminar usuario — orden correcto de tablas
     */
    public function eliminar($id_usuario) {
        $id = (int)$id_usuario;
        if ($id <= 0) return false;
        
        try {
            // Desvincular tutorías
            $stmt = $this->conexion->prepare("UPDATE tutorias SET id_tutor = NULL WHERE id_tutor = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar asignaciones tutor-materia
            $stmt = $this->conexion->prepare("DELETE FROM tutor_materia WHERE id_tutor = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar expedientes del estudiante
            $stmt = $this->conexion->prepare("DELETE FROM expedientes WHERE id_estudiante IN 
                (SELECT id_estudiante FROM estudiantes WHERE id_usuario = :id)");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar notificaciones
            $stmt = $this->conexion->prepare("DELETE FROM notificaciones WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar estudiante
            $stmt = $this->conexion->prepare("DELETE FROM estudiantes WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar tutor
            $stmt = $this->conexion->prepare("DELETE FROM tutores WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Eliminar usuario
            $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_usuario = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            error_log("NO se pudo eliminar: " . $e->getMessage());
            return false;
        }
    }
}