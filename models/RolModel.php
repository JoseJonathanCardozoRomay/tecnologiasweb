<?php
/**
 * Modelo Rol
 */
require_once __DIR__ . '/../config/conexion.php';

class RolModel {
    private $conexion;
    private $tabla = 'roles';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $stmt = $this->conexion->query("SELECT * FROM {$this->tabla} ORDER BY id_rol");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $stmt = $this->conexion->prepare("SELECT * FROM {$this->tabla} WHERE id_rol = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre) {
        try {
            $stmt = $this->conexion->prepare("INSERT INTO {$this->tabla} (nombre_rol) VALUES (:nombre)");
            $stmt->bindParam(':nombre', $nombre);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $nombre) {
        try {
            $stmt = $this->conexion->prepare("UPDATE {$this->tabla} SET nombre_rol = :nombre WHERE id_rol = :id");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_rol = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}