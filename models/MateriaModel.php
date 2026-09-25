<?php
/**
 * Modelo para la gestión de Materias
 */
require_once __DIR__ . '/../config/conexion.php';

class MateriaModel {
    private $conexion;
    private $tabla = 'materias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtener todas las materias con nombre de carrera
     */
    public function listarTodasConCarrera() {
        $sql = "SELECT m.*, c.nombre_carrera
                FROM {$this->tabla} m
                LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                ORDER BY m.nombre_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todas las materias
     */
    public function listarTodas() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY nombre_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una materia por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nueva materia
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (nombre_materia, id_carrera) 
                VALUES (:nombre_materia, :id_carrera)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_materia', $datos['nombre_materia']);
        $stmt->bindParam(':id_carrera', $datos['id_carrera'], PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Editar materia
     */
    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET 
                    nombre_materia = :nombre_materia,
                    id_carrera = :id_carrera
                WHERE id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_materia', $datos['nombre_materia']);
        $stmt->bindParam(':id_carrera', $datos['id_carrera'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Eliminar materia
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_materia = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}