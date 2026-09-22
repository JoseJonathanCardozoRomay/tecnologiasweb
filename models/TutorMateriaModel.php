<?php
/**
 * Modelo para la gestión de la relación N:M Tutor–Materia
 * Sistema de Gestión de Tutorías
 */
require_once __DIR__ . '/../config/conexion.php';

class TutorMateriaModel {
    private $conexion;
    private $tabla = 'tutor_materia';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todas las asignaciones con datos del tutor y la materia
     */
    public function listarTodos() {
        $consulta = "SELECT tm.id_tutor, tm.id_materia,
                            u.nombre, u.apellido,
                            m.nombre_materia
                     FROM {$this->tabla} tm
                     INNER JOIN tutores t ON tm.id_tutor = t.id_tutor
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     INNER JOIN materias m ON tm.id_materia = m.id_materia
                     ORDER BY u.apellido, u.nombre, m.nombre_materia";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene materias asignadas a un tutor
     */
    public function obtenerPorTutor($id_tutor) {
        $consulta = "SELECT tm.id_materia, m.nombre_materia
                     FROM {$this->tabla} tm
                     INNER JOIN materias m ON tm.id_materia = m.id_materia
                     WHERE tm.id_tutor = :id_tutor
                     ORDER BY m.nombre_materia";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Materias disponibles para asignar
     */
    public function materiasDisponibles($id_tutor) {
        $consulta = "SELECT m.id_materia, m.nombre_materia
                     FROM materias m
                     LEFT JOIN {$this->tabla} tm 
                        ON m.id_materia = tm.id_materia AND tm.id_tutor = :id_tutor
                     WHERE tm.id_materia IS NULL
                     ORDER BY m.nombre_materia";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista tutores con cantidad de materias
     */
    public function listarTutores() {
        $consulta = "SELECT t.id_tutor, u.nombre, u.apellido,
                            COUNT(tm.id_materia) AS total_materias
                     FROM tutores t
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     LEFT JOIN {$this->tabla} tm ON t.id_tutor = tm.id_tutor
                     GROUP BY t.id_tutor, u.nombre, u.apellido
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear asignación
     */
    public function crear($id_tutor, $id_materia) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (id_tutor, id_materia) 
                         VALUES (:id_tutor, :id_materia)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
            $sentencia->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'Esta materia ya está asignada al tutor'];
            }
            return ['error' => 'Error al asignar la materia'];
        }
    }

    /**
     * Eliminar asignación
     */
    public function eliminar($id_tutor, $id_materia) {
        try {
            $consulta = "DELETE FROM {$this->tabla} 
                         WHERE id_tutor = :id_tutor AND id_materia = :id_materia";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
            $sentencia->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            return ['error' => 'No se pudo eliminar la asignación'];
        }
    }

    /**
     * Verificar si el tutor existe
     */
    public function existeTutor($id_tutor) {
        $consulta = "SELECT 1 FROM tutores WHERE id_tutor = :id_tutor LIMIT 1";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch() !== false;
    }
}