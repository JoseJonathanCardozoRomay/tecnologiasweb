<?php
require_once __DIR__ . '/../config/conexion.php';

class BloqueHorarioModel {
    private $conexion;
    private $tabla = 'bloques_horarios';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY hora_inicio";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        try {
            $sql = "INSERT INTO {$this->tabla} (nombre_bloque, hora_inicio, hora_fin, descripcion)
                    VALUES (:nombre, :hinicio, :hfin, :descripcion)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre_bloque']);
            $stmt->bindParam(':hinicio', $datos['hora_inicio']);
            $stmt->bindParam(':hfin', $datos['hora_fin']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_bloque = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla} 
                    SET nombre_bloque = :nombre,
                        hora_inicio = :hinicio,
                        hora_fin = :hfin,
                        descripcion = :descripcion
                    WHERE id_bloque = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre', $datos['nombre_bloque']);
            $stmt->bindParam(':hinicio', $datos['hora_inicio']);
            $stmt->bindParam(':hfin', $datos['hora_fin']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_bloque = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}