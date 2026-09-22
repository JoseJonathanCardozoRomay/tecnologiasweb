<?php
require_once __DIR__ . '/../config/conexion.php';

class TutoriaModel {
    private $conexion;
    private $tabla = 'tutorias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT t.*,
                       e.nombre AS est_nombre, e.apellido AS est_apellido,
                       tu.nombre AS tut_nombre, tu.apellido AS tut_apellido,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                ORDER BY t.fecha DESC, t.hora_inicio DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarEstudiantes() {
        $sql = "SELECT e.id_estudiante, u.nombre, u.apellido
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarTutores() {
        $sql = "SELECT t.id_tutor, u.nombre, u.apellido
                FROM tutores t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function listarMaterias() {
        $sql = "SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        try {
            $estado = !empty($datos['estado']) ? $datos['estado'] : 'pendiente';
            $sql = "INSERT INTO {$this->tabla} (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, observaciones, estado)
                    VALUES (:idest, :idtut, :idmat, :fecha, :hinicio, :hfin, :modalidad, :lugar, :obs, :estado)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idest', $datos['id_estudiante']);
            $stmt->bindParam(':idtut', $datos['id_tutor']);
            $stmt->bindParam(':idmat', $datos['id_materia']);
            $stmt->bindParam(':fecha', $datos['fecha']);
            $stmt->bindParam(':hinicio', $datos['hora_inicio']);
            $stmt->bindParam(':hfin', $datos['hora_fin']);
            $stmt->bindParam(':modalidad', $datos['modalidad']);
            $stmt->bindParam(':lugar', $datos['lugar_o_enlace']);
            $stmt->bindParam(':obs', $datos['observaciones']);
            $stmt->bindParam(':estado', $estado);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT t.*,
                       e.nombre AS est_nombre, e.apellido AS est_apellido,
                       tu.nombre AS tut_nombre, tu.apellido AS tut_apellido,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE t.id_tutoria = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        try {
            $estado = !empty($datos['estado']) ? $datos['estado'] : 'pendiente';
            $sql = "UPDATE {$this->tabla} 
                    SET id_estudiante = :idest,
                        id_tutor = :idtut,
                        id_materia = :idmat,
                        fecha = :fecha,
                        hora_inicio = :hinicio,
                        hora_fin = :hfin,
                        modalidad = :modalidad,
                        lugar_o_enlace = :lugar,
                        observaciones = :obs,
                        estado = :estado
                    WHERE id_tutoria = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idest', $datos['id_estudiante']);
            $stmt->bindParam(':idtut', $datos['id_tutor']);
            $stmt->bindParam(':idmat', $datos['id_materia']);
            $stmt->bindParam(':fecha', $datos['fecha']);
            $stmt->bindParam(':hinicio', $datos['hora_inicio']);
            $stmt->bindParam(':hfin', $datos['hora_fin']);
            $stmt->bindParam(':modalidad', $datos['modalidad']);
            $stmt->bindParam(':lugar', $datos['lugar_o_enlace']);
            $stmt->bindParam(':obs', $datos['observaciones']);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_tutoria = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}