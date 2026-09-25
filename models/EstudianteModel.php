<?php
/**
 * Modelo Estudiante
 */
require_once __DIR__ . '/../config/conexion.php';

class EstudianteModel {
    private $conexion;
    private $tabla = 'estudiantes';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorUsuario($id_usuario) {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_estudiante) {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id_estudiante";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($id_usuario, $id_carrera, $semestre, $registro_universitario) {
        $sql = "INSERT INTO {$this->tabla} (id_usuario, id_carrera, semestre, registro_universitario)
                VALUES (:id_usuario, :id_carrera, :semestre, :registro_universitario)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':id_carrera', $id_carrera, PDO::PARAM_INT);
        $stmt->bindParam(':semestre', $semestre, PDO::PARAM_INT);
        $stmt->bindParam(':registro_universitario', $registro_universitario);
        return $stmt->execute();
    }

    public function actualizar($id_estudiante, $id_carrera, $semestre, $registro_universitario) {
        $sql = "UPDATE {$this->tabla}
                SET id_carrera = :id_carrera, semestre = :semestre, registro_universitario = :registro
                WHERE id_estudiante = :id_estudiante";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_carrera', $id_carrera, PDO::PARAM_INT);
        $stmt->bindParam(':semestre', $semestre, PDO::PARAM_INT);
        $stmt->bindParam(':registro', $registro_universitario);
        $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id_estudiante) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_estudiante = :id_estudiante";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_estudiante', $id_estudiante, PDO::PARAM_INT);
        return $stmt->execute();
    }
}