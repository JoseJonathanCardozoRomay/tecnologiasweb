<?php
require_once __DIR__ . '/../config/conexion.php';
class UsuarioModel {
    private $conexion;
    private $tabla = 'usuarios';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $consulta = "SELECT u.*, r.nombre_rol 
                     FROM {$this->tabla} u 
                     LEFT JOIN roles r ON u.id_rol = r.id_rol
                     ORDER BY u.id_usuario DESC";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT * FROM {$this->tabla} WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarRoles() {
        $consulta = "SELECT * FROM roles ORDER BY id_rol";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        $consulta = "INSERT INTO {$this->tabla} 
                     (id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado)
                     VALUES (:id_rol, :nombre, :apellido, :correo, :usuario, :contrasena, :telefono, :estado)";
        $stmt = $this->conexion->prepare($consulta);
        
        $contrasenaHash = password_hash($datos['contrasena'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':id_rol', $datos['id_rol']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':usuario', $datos['usuario']);
        $stmt->bindParam(':contrasena', $contrasenaHash);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        return $stmt->execute();
    }

    public function actualizar($id, $datos) {
        $consulta = "UPDATE {$this->tabla} SET 
                     id_rol = :id_rol,
                     nombre = :nombre,
                     apellido = :apellido,
                     correo = :correo,
                     usuario = :usuario,
                     telefono = :telefono,
                     estado = :estado
                     WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($consulta);
        
        $stmt->bindParam(':id_rol', $datos['id_rol']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':apellido', $datos['apellido']);
        $stmt->bindParam(':correo', $datos['correo']);
        $stmt->bindParam(':usuario', $datos['usuario']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':estado', $datos['estado']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function eliminar($id) {
        $consulta = "DELETE FROM {$this->tabla} WHERE id_usuario = :id";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ✅ MÉTODO NUEVO — NO TOCAR LO DE ARRIBA
    public function listarSinRolAsignado() {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.usuario, r.nombre_rol
                FROM {$this->tabla} u
                LEFT JOIN roles r ON u.id_rol = r.id_rol
                WHERE r.nombre_rol IS NULL 
                   OR r.nombre_rol NOT IN ('estudiante', 'tutor', 'administrador')
                ORDER BY u.nombre, u.apellido";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ✅ FIN MÉTODO NUEVO
}