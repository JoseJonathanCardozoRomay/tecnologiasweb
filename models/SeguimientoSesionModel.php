<?php
require_once __DIR__ . '/../config/conexion.php';

class SeguimientoSesionModel {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listar() {
        $sql = "SELECT s.*, t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin,
                       est.nombre AS estudiante_nombre, est.apellido AS estudiante_apellido,
                       tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                       mat.nombre_materia
                FROM seguimiento_sesion s
                LEFT JOIN tutorias t ON s.id_tutoria = t.id_tutoria
                LEFT JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                LEFT JOIN usuarios est ON e.id_usuario = est.id_usuario
                LEFT JOIN tutores tu ON t.id_tutor = tu.id_tutor
                LEFT JOIN usuarios tut ON tu.id_usuario = tut.id_usuario
                LEFT JOIN materias mat ON t.id_materia = mat.id_materia
                ORDER BY s.fecha_registro DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_seguimiento) {
        $sql = "SELECT s.*, t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin, t.estado AS estado_tutoria
                FROM seguimiento_sesion s
                LEFT JOIN tutorias t ON s.id_tutoria = t.id_tutoria
                WHERE s.id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_seguimiento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($id_tutoria, $asistio, $temas_tratados = null, $avance = null, $recomendaciones = null) {
        $sql = "INSERT INTO seguimiento_sesion (id_tutoria, asistio, temas_tratados, avance, recomendaciones)
                VALUES (:id_tutoria, :asistio, :temas_tratados, :avance, :recomendaciones)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $id_tutoria, PDO::PARAM_INT);
        $stmt->bindParam(':asistio', $asistio);
        $stmt->bindParam(':temas_tratados', $temas_tratados);
        $stmt->bindParam(':avance', $avance);
        $stmt->bindParam(':recomendaciones', $recomendaciones);
        return $stmt->execute();
    }

    public function actualizar($id_seguimiento, $asistio, $temas_tratados = null, $avance = null, $recomendaciones = null) {
        $sql = "UPDATE seguimiento_sesion
                SET asistio = :asistio,
                    temas_tratados = :temas_tratados,
                    avance = :avance,
                    recomendaciones = :recomendaciones
                WHERE id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':asistio', $asistio);
        $stmt->bindParam(':temas_tratados', $temas_tratados);
        $stmt->bindParam(':avance', $avance);
        $stmt->bindParam(':recomendaciones', $recomendaciones);
        $stmt->bindParam(':id', $id_seguimiento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id_seguimiento) {
        $sql = "DELETE FROM seguimiento_sesion WHERE id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_seguimiento, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarTutoriasSinSeguimiento() {
        $sql = "SELECT t.id_tutoria, t.fecha, t.hora_inicio, t.hora_fin, t.estado
                FROM tutorias t
                LEFT JOIN seguimiento_sesion s ON t.id_tutoria = s.id_tutoria
                WHERE s.id_seguimiento IS NULL
                ORDER BY t.fecha DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>