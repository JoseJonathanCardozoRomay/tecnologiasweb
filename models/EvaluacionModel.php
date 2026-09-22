<?php
require_once __DIR__ . '/../config/conexion.php';

class EvaluacionModel {
    private $conexion;
    private $tabla = 'evaluaciones_tutoria';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT e.*, t.fecha, t.estado AS estado_tutoria,
                       est.nombre AS est_nombre, est.apellido AS est_apellido,
                       tu.nombre AS tut_nombre, tu.apellido AS tut_apellido
                FROM {$this->tabla} e
                LEFT JOIN tutorias t ON e.id_tutoria = t.id_tutoria
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios u_est ON est.id_usuario = u_est.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios u_tut ON tut.id_usuario = u_tut.id_usuario
                ORDER BY e.fecha_evaluacion DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId($id) {
        $sql = "SELECT e.*, t.fecha, t.estado AS estado_tutoria
                FROM {$this->tabla} e
                LEFT JOIN tutorias t ON e.id_tutoria = t.id_tutoria
                WHERE e.id_evaluacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTutoriasDisponibles() {
        $sql = "SELECT id_tutoria, fecha, estado FROM tutorias ORDER BY fecha DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        try {
            $sql = "INSERT INTO {$this->tabla} (id_tutoria, calificacion, comentario)
                    VALUES (:idtut, :calif, :coment)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idtut', $datos['id_tutoria']);
            $stmt->bindParam(':calif', $datos['calificacion']);
            $stmt->bindParam(':coment', $datos['comentario']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla}
                    SET id_tutoria = :idtut,
                        calificacion = :calif,
                        comentario = :coment
                    WHERE id_evaluacion = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idtut', $datos['id_tutoria']);
            $stmt->bindParam(':calif', $datos['calificacion']);
            $stmt->bindParam(':coment', $datos['comentario']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_evaluacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}