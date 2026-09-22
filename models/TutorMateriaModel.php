<?php
/**
 * Modelo Tutor-Materia
 */
require_once __DIR__ . '/../config/conexion.php';

class TutorMateriaModel {
    private $conexion;
    private $tabla = 'tutor_materia';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    // ✅ CONSULTA CORREGIDA — ahora sí trae los nombres
    public function listarTodo() {
        $sql = "SELECT tm.id_tutor, tm.id_materia,
                       u.nombre AS nombre_tutor, u.apellido AS apellido_tutor,
                       m.nombre_materia
                FROM {$this->tabla} tm
                INNER JOIN tutores t ON tm.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                ORDER BY u.nombre, m.nombre_materia";
        
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar($id_tutor, $id_materia) {
        try {
            $stmt = $this->conexion->prepare("INSERT INTO {$this->tabla} (id_tutor, id_materia) VALUES (:id_tutor, :id_materia)");
            $stmt->bindParam(':id_tutor', $id_tutor);
            $stmt->bindParam(':id_materia', $id_materia);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function quitar($id_tutor, $id_materia) {
        $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_tutor = :id_tutor AND id_materia = :id_materia");
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->bindParam(':id_materia', $id_materia);
        return $stmt->execute();
    }
}