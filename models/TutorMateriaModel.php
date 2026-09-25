<?php
/**
 * Modelo Tutor-Materia
 * Gestiona la relación N:M entre Tutores y Materias
 */

require_once __DIR__ . '/../config/conexion.php';

class TutorMateriaModel {
    private $conexion;
    private $tabla = 'tutor_materia';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    // ✅ ESTE ES EL MÉTODO QUE FALTABA
    public function listarTodas() {
        $sql = "SELECT tm.*, 
                       t.id_tutor, u.nombre, u.apellido,
                       m.id_materia, m.nombre_materia
                FROM {$this->tabla} tm
                INNER JOIN tutores t ON tm.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                ORDER BY u.nombre, u.apellido, m.nombre_materia";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignar($id_tutor, $id_materia) {
        $verificar = "SELECT 1 FROM {$this->tabla} 
                      WHERE id_tutor = :id_tutor AND id_materia = :id_materia";
        $stmt = $this->conexion->prepare($verificar);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return false;
        }

        $sql = "INSERT INTO {$this->tabla} (id_tutor, id_materia) 
                VALUES (:id_tutor, :id_materia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function quitar($id_tutor, $id_materia) {
        $sql = "DELETE FROM {$this->tabla} 
                WHERE id_tutor = :id_tutor AND id_materia = :id_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function materiasPorTutor($id_tutor) {
        $sql = "SELECT m.*
                FROM {$this->tabla} tm
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                WHERE tm.id_tutor = :id_tutor
                ORDER BY m.nombre_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}