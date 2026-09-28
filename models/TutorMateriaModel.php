<?php
/**
 * Modelo Tutor-Materia — Completo ✅
 * Asignar ✅ Quitar ✅ Listar ✅ Aceptar ✅ Rechazar ✅ Estados ✅
 * ✅ Sin romper nada de lo que ya funciona
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
     * Listar todas las asignaciones
     */
    public function listarTodas() {
        $sql = "SELECT tm.*, 
                       t.id_tutor, u.nombre, u.apellido,
                       m.id_materia, m.nombre_materia,
                       tm.estado
                FROM {$this->tabla} tm
                INNER JOIN tutores t ON tm.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                ORDER BY u.nombre, u.apellido, m.nombre_materia";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Admin asigna → queda PENDIENTE
     */
    public function asignar($id_tutor, $id_materia) {
        $id_tutor = (int)$id_tutor;
        $id_materia = (int)$id_materia;

        // Verificar si ya existe
        $verificar = "SELECT 1 FROM {$this->tabla} 
                      WHERE id_tutor = :id_tutor AND id_materia = :id_materia";
        $stmt = $this->conexion->prepare($verificar);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return ['existe' => true];
        }

        // ✅ Insertar — si tu tabla NO tiene estos campos, avísame
        $sql = "INSERT INTO {$this->tabla} (id_tutor, id_materia, estado, fecha_asignacion) 
                VALUES (:id_tutor, :id_materia, 'pendiente', NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Quitar asignación
     */
    public function quitar($id_tutor, $id_materia) {
        $sql = "DELETE FROM {$this->tabla} 
                WHERE id_tutor = :id_tutor AND id_materia = :id_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Eliminar por ID
     */
    public function eliminar($id) {
        $id = (int)$id;
        $sql = "DELETE FROM {$this->tabla} WHERE id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Materias que ya tiene el tutor
     */
    public function materiasPorTutor($id_tutor) {
        $id_tutor = (int)$id_tutor;
        $sql = "SELECT m.*, tm.estado
                FROM {$this->tabla} tm
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                WHERE tm.id_tutor = :id_tutor
                ORDER BY m.nombre_materia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar SOLO las PENDIENTES para que el tutor decida
     */
    public function listarPendientesPorTutor($id_tutor) {
        $id_tutor = (int)$id_tutor;
        $sql = "SELECT tm.*, m.nombre_materia, tm.fecha_asignacion AS fecha_solicitud
                FROM {$this->tabla} tm
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                WHERE tm.id_tutor = :id_tutor AND tm.estado = 'pendiente'
                ORDER BY tm.fecha_asignacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tutor ACEPTA la materia
     */
    public function aceptar($id) {
        $id = (int)$id;
        $sql = "UPDATE {$this->tabla} 
                SET estado = 'aceptada', fecha_respuesta = NOW() 
                WHERE id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Tutor RECHAZA con motivo
     */
    public function rechazar($id, $motivo = '') {
        $id = (int)$id;
        $sql = "UPDATE {$this->tabla} 
                SET estado = 'rechazada', motivo_rechazo = :motivo, fecha_respuesta = NOW() 
                WHERE id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Solo las ACEPTADAS para mostrar en su lista
     */
    public function listarAceptadasPorTutor($id_tutor) {
        $id_tutor = (int)$id_tutor;
        $sql = "SELECT m.*
                FROM {$this->tabla} tm
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                WHERE tm.id_tutor = :id_tutor AND tm.estado = 'aceptada'
                ORDER BY m.nombre_materia ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una asignación por su ID
     */
    public function obtenerPorId($id) {
        $id = (int)$id;
        $sql = "SELECT tm.*, u.nombre, u.apellido, m.nombre_materia
                FROM {$this->tabla} tm
                INNER JOIN tutores t ON tm.id_tutor = t.id_tutor
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                INNER JOIN materias m ON tm.id_materia = m.id_materia
                WHERE tm.id_asignacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}