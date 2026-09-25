<?php
/**
 * Modelo Tutoría — Nombres correctos y separados definitivamente
 */
require_once __DIR__ . '/../config/conexion.php';
class TutoriaModel {
    private $conexion;
    private $tabla = 'tutorias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    // ✅ TODAS las tutorías — nombres separados
    public function listarTodas() {
        $sql = "SELECT t.*,
                       -- ESTUDIANTE
                       u_est.nombre AS nombre_estudiante,
                       u_est.apellido AS apellido_estudiante,
                       -- TUTOR
                       u_tut.nombre AS nombre_tutor,
                       u_tut.apellido AS apellido_tutor,
                       -- MATERIA
                       m.nombre_materia
                FROM {$this->tabla} t
                -- Estudiante: tutorias → estudiantes → usuarios
                LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                LEFT JOIN usuarios u_est ON e.id_usuario = u_est.id_usuario
                -- Tutor: tutorias → usuarios directo
                LEFT JOIN usuarios u_tut ON t.id_tutor = u_tut.id_usuario
                -- Materia
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                ORDER BY t.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Solo las del estudiante que inició sesión
    public function listarPorEstudiante($id_usuario) {
        $sql = "SELECT t.*,
                       u_est.nombre AS nombre_estudiante,
                       u_est.apellido AS apellido_estudiante,
                       u_tut.nombre AS nombre_tutor,
                       u_tut.apellido AS apellido_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                LEFT JOIN usuarios u_est ON e.id_usuario = u_est.id_usuario
                LEFT JOIN usuarios u_tut ON t.id_tutor = u_tut.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE e.id_usuario = :id_usuario
                ORDER BY t.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Solo las asignadas al tutor
    public function listarPorTutor($id_usuario) {
        $sql = "SELECT t.*,
                       u_est.nombre AS nombre_estudiante,
                       u_est.apellido AS apellido_estudiante,
                       u_tut.nombre AS nombre_tutor,
                       u_tut.apellido AS apellido_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                LEFT JOIN usuarios u_est ON e.id_usuario = u_est.id_usuario
                LEFT JOIN usuarios u_tut ON t.id_tutor = u_tut.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE t.id_tutor = :id_usuario
                ORDER BY t.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_tutoria) {
        $sql = "SELECT t.*,
                       u_est.nombre AS nombre_estudiante,
                       u_est.apellido AS apellido_estudiante,
                       u_tut.nombre AS nombre_tutor,
                       u_tut.apellido AS apellido_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                LEFT JOIN usuarios u_est ON e.id_usuario = u_est.id_usuario
                LEFT JOIN usuarios u_tut ON t.id_tutor = u_tut.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE t.id_tutoria = :id_tutoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $id_tutoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($id_estudiante, $id_tutor, $id_materia, $fecha, $hora_inicio, $hora_fin, $modalidad, $lugar_o_enlace) {
        $sql = "INSERT INTO {$this->tabla} (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado)
                VALUES (:id_est, :id_tut, :id_mat, :fecha, :hini, :hfin, :mod, :lugar, 'pendiente')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_est', $id_estudiante, PDO::PARAM_INT);
        $stmt->bindParam(':id_tut', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_mat', $id_materia, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hini', $hora_inicio);
        $stmt->bindParam(':hfin', $hora_fin);
        $stmt->bindParam(':mod', $modalidad);
        $stmt->bindParam(':lugar', $lugar_o_enlace);
        return $stmt->execute();
    }

    public function actualizar($id_tutoria, $id_estudiante, $id_tutor, $id_materia, $fecha, $hora_inicio, $hora_fin, $modalidad, $lugar_o_enlace, $estado) {
        $sql = "UPDATE {$this->tabla}
                SET id_estudiante = :id_est, id_tutor = :id_tut, id_materia = :id_mat,
                    fecha = :fecha, hora_inicio = :hini, hora_fin = :hfin,
                    modalidad = :mod, lugar_o_enlace = :lugar, estado = :estado
                WHERE id_tutoria = :id_tutoria";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $id_tutoria, PDO::PARAM_INT);
        $stmt->bindParam(':id_est', $id_estudiante, PDO::PARAM_INT);
        $stmt->bindParam(':id_tut', $id_tutor, PDO::PARAM_INT);
        $stmt->bindParam(':id_mat', $id_materia, PDO::PARAM_INT);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hini', $hora_inicio);
        $stmt->bindParam(':hfin', $hora_fin);
        $stmt->bindParam(':mod', $modalidad);
        $stmt->bindParam(':lugar', $lugar_o_enlace);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();
    }

    public function eliminar($id_tutoria) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_tutoria = ?";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id_tutoria]);
    }
}