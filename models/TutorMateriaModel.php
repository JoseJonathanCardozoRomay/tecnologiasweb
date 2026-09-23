<?php
require_once __DIR__ . '/../config/conexion.php';

class TutorMateriaModel {
    private $conexion;
    private $tabla = 'tutor_materia';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Listar todas las relaciones con nombres completos
     */
    public function listarTodos() {
        $sql = "SELECT tm.*, 
                       u.nombre, u.apellido,
                       m.nombre_materia
                FROM {$this->tabla} tm
                LEFT JOIN tutores t ON tm.id_tutor = t.id_tutor
                LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                LEFT JOIN materias m ON tm.id_materia = m.id_materia
                ORDER BY tm.id_tutor DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener lista de tutores para desplegable
     */
    public function listarTutores() {
        $sql = "SELECT t.id_tutor, u.nombre, u.apellido
                FROM tutores t
                LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY u.nombre";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener lista de materias para desplegable
     */
    public function listarMaterias() {
        $sql = "SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Asignar materia a tutor
     */
    public function asignar($id_tutor, $id_materia) {
        try {
            $sql = "INSERT INTO {$this->tabla} (id_tutor, id_materia) VALUES (:idt, :idm)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idt', $id_tutor, PDO::PARAM_INT);
            $stmt->bindParam(':idm', $id_materia, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Quitar materia a tutor
     */
    public function quitar($id_tutor, $id_materia) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_tutor = :idt AND id_materia = :idm";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idt', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':idm', $id_materia, PDO::PARAM_INT);
        return $stmt->execute();
    }
}