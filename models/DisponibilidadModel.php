<?php
/**
 * Modelo para la gestión de Disponibilidad Horaria de Tutores
 * Permite al tutor registrar sus días y horas disponibles
 */

require_once __DIR__ . '/../config/conexion.php';

class DisponibilidadModel {
    private $conexion;
    private $tabla = 'disponibilidad_tutor';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtener TODOS los horarios con nombre del tutor
     */
    public function listarTodos() {
        $sql = "SELECT d.*, 
                       CONCAT(u.nombre, ' ', u.apellido) AS tutor_nombre,
                       u.id_usuario
                FROM {$this->tabla} d
                INNER JOIN tutores t ON d.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY 
                    FIELD(d.dia_semana, 'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'),
                    d.hora_inicio";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener SOLO los horarios de un tutor específico
     */
    public function listarPorTutor($id_tutor) {
        $sql = "SELECT d.*, 
                       CONCAT(u.nombre, ' ', u.apellido) AS tutor_nombre,
                       u.id_usuario
                FROM {$this->tabla} d
                INNER JOIN tutores t ON d.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE d.id_tutor = :id_tutor
                ORDER BY 
                    FIELD(d.dia_semana, 'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'),
                    d.hora_inicio";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un horario por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT d.*, 
                       CONCAT(u.nombre, ' ', u.apellido) AS tutor_nombre,
                       u.id_usuario
                FROM {$this->tabla} d
                INNER JOIN tutores t ON d.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE d.id_disponibilidad = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo horario de disponibilidad
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (id_tutor, dia_semana, hora_inicio, hora_fin)
                VALUES (:id_tutor, :dia_semana, :hora_inicio, :hora_fin)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $datos['id_tutor'], PDO::PARAM_INT);
        $stmt->bindParam(':dia_semana', $datos['dia_semana']);
        $stmt->bindParam(':hora_inicio', $datos['hora_inicio']);
        $stmt->bindParam(':hora_fin', $datos['hora_fin']);
        
        return $stmt->execute();
    }

    /**
     * Actualizar horario existente
     */
    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} 
                SET dia_semana = :dia_semana,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin
                WHERE id_disponibilidad = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':dia_semana', $datos['dia_semana']);
        $stmt->bindParam(':hora_inicio', $datos['hora_inicio']);
        $stmt->bindParam(':hora_fin', $datos['hora_fin']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Eliminar un horario
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_disponibilidad = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Verificar si el horario pertenece al tutor (para seguridad)
     */
    public function perteneceATutor($id_disponibilidad, $id_tutor) {
        $sql = "SELECT COUNT(*) FROM {$this->tabla} 
                WHERE id_disponibilidad = :id AND id_tutor = :id_tutor";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_disponibilidad, PDO::PARAM_INT);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}