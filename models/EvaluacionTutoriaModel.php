<?php
/**
 * Modelo Evaluaciones de Tutoría
 * Sistema de Gestión de Tutorías
 */
require_once __DIR__ . '/../config/conexion.php';

class EvaluacionTutoriaModel {
    private $conexion;
    private $tabla = 'evaluaciones_tutoria';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $consulta = "SELECT e.id_evaluacion, e.calificacion, e.comentario, e.fecha_evaluacion,
                            t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin, t.estado,
                            est.codigo_estudiante,
                            ue.nombre AS nom_est, ue.apellido AS ape_est,
                            ut.nombre AS nom_tut, ut.apellido AS ape_tut,
                            m.nombre_materia
                     FROM {$this->tabla} e
                     INNER JOIN tutorias t ON e.id_tutoria = t.id_tutoria
                     INNER JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                     INNER JOIN usuarios ue ON est.id_usuario = ue.id_usuario
                     INNER JOIN tutores tut ON t.id_tutor = tut.id_tutor
                     INNER JOIN usuarios ut ON tut.id_usuario = ut.id_usuario
                     INNER JOIN materias m ON t.id_materia = m.id_materia
                     ORDER BY e.fecha_evaluacion DESC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT e.*,
                            t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin,
                            ue.nombre AS nom_est, ue.apellido AS ape_est,
                            ut.nombre AS nom_tut, ut.apellido AS ape_tut,
                            m.nombre_materia
                     FROM {$this->tabla} e
                     INNER JOIN tutorias t ON e.id_tutoria = t.id_tutoria
                     INNER JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                     INNER JOIN usuarios ue ON est.id_usuario = ue.id_usuario
                     INNER JOIN tutores tut ON t.id_tutor = tut.id_tutor
                     INNER JOIN usuarios ut ON tut.id_usuario = ut.id_usuario
                     INNER JOIN materias m ON t.id_materia = m.id_materia
                     WHERE e.id_evaluacion = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (id_tutoria, calificacion, comentario)
                         VALUES (:id_tutoria, :calificacion, :comentario)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id_tutoria'   => $datos['id_tutoria'],
                ':calificacion' => $datos['calificacion'],
                ':comentario'   => $datos['comentario']
            ]);
            return true;
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return ['error' => 'Esta tutoría ya fue evaluada'];
            }
            return ['error' => 'Error al registrar la evaluación'];
        }
    }

    public function editar($id, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET
                calificacion = :calificacion,
                comentario   = :comentario
                WHERE id_evaluacion = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id'              => $id,
                ':calificacion'    => $datos['calificacion'],
                ':comentario'      => $datos['comentario']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al actualizar la evaluación'];
        }
    }

    public function eliminar($id) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_evaluacion = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $e) {
            return ['error' => 'No se pudo eliminar'];
        }
    }

    public function listarTutoriasSinEvaluar() {
        $consulta = "SELECT t.id_tutoria, t.fecha, t.hora_inicio, t.hora_fin,
                            ue.nombre AS nom_est, ue.apellido AS ape_est,
                            ut.nombre AS nom_tut, ut.apellido AS ape_tut,
                            m.nombre_materia
                     FROM tutorias t
                     INNER JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                     INNER JOIN usuarios ue ON est.id_usuario = ue.id_usuario
                     INNER JOIN tutores tut ON t.id_tutor = tut.id_tutor
                     INNER JOIN usuarios ut ON tut.id_usuario = ut.id_usuario
                     INNER JOIN materias m ON t.id_materia = m.id_materia
                     LEFT JOIN {$this->tabla} e ON t.id_tutoria = e.id_tutoria
                     WHERE e.id_evaluacion IS NULL
                     ORDER BY t.fecha DESC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}