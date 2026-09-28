<?php
/**
 * Modelo Materia — Completo
 */
require_once __DIR__ . '/../config/conexion.php';

class MateriaModel {
    private $conexion;
    private $tabla = 'materias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT m.*, c.nombre_carrera 
                FROM {$this->tabla} m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                ORDER BY m.nombre_materia ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ MÉTODO AGREGADO — El que faltaba
    public function listarTodas() {
        return $this->listarTodos();
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $sql = "SELECT m.*, c.nombre_carrera 
                FROM {$this->tabla} m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                WHERE m.id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $nombre = trim($datos['nombre_materia'] ?? '');
        $id_carrera = !empty($datos['id_carrera']) ? (int)$datos['id_carrera'] : null;

        if (empty($nombre)) return false;

        $sql = "INSERT INTO {$this->tabla} (nombre_materia, id_carrera) VALUES (:nombre, :id_carrera)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id_carrera', $id_carrera);
        return $stmt->execute();
    }

    public function actualizar($id, $datos) {
        $id = (int)$id;
        $nombre = trim($datos['nombre_materia'] ?? '');
        $id_carrera = !empty($datos['id_carrera']) ? (int)$datos['id_carrera'] : null;

        if ($id <= 0 || empty($nombre)) return false;

        $sql = "UPDATE {$this->tabla} SET nombre_materia = :nombre, id_carrera = :id_carrera WHERE id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':id_carrera', $id_carrera);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $id = (int)$id;
        if ($id <= 0) return false;

        $sql = "DELETE FROM {$this->tabla} WHERE id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}