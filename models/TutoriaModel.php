<?php
/**
 * Modelo Tutoría — Completo y corregido
 */
require_once __DIR__ . '/../config/conexion.php';
class TutoriaModel {
    private $conexion;
    private $tabla = 'tutorias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT t.*,
                       CONCAT(e.nombre, ' ', e.apellido) AS nombre_estudiante,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS nombre_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                ORDER BY t.fecha DESC, t.hora_inicio DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodasCompletas() {
        return $this->listarTodos();
    }

    public function listarPorTutorUsuario($id_usuario) {
        $sql = "SELECT t.*,
                       CONCAT(e.nombre, ' ', e.apellido) AS nombre_estudiante,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS nombre_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE tut.id_usuario = :id_usuario
                ORDER BY t.fecha DESC, t.hora_inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorEstudiante($id_usuario) {
        $sql = "SELECT t.*,
                       CONCAT(e.nombre, ' ', e.apellido) AS nombre_estudiante,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS nombre_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE est.id_usuario = :id_usuario
                ORDER BY t.fecha DESC, t.hora_inicio DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorTutor($id_usuario) {
        return $this->listarPorTutorUsuario($id_usuario);
    }

    public function obtenerPorId($id_tutoria) {
        $sql = "SELECT t.*,
                       CONCAT(e.nombre, ' ', e.apellido) AS nombre_estudiante,
                       CONCAT(tu.nombre, ' ', tu.apellido) AS nombre_tutor,
                       m.nombre_materia
                FROM {$this->tabla} t
                LEFT JOIN estudiantes est ON t.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios e ON est.id_usuario = e.id_usuario
                LEFT JOIN tutores tut ON t.id_tutor = tut.id_tutor
                LEFT JOIN usuarios tu ON tut.id_usuario = tu.id_usuario
                LEFT JOIN materias m ON t.id_materia = m.id_materia
                WHERE t.id_tutoria = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_tutoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla}
                (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado, observaciones)
                VALUES (:id_estudiante, :id_tutor, :id_materia, :fecha, :h_inicio, :h_fin, :modalidad, :lugar, :estado, :observaciones)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_estudiante' => $datos['id_estudiante'],
            ':id_tutor' => $datos['id_tutor'],
            ':id_materia' => $datos['id_materia'],
            ':fecha' => $datos['fecha'],
            ':h_inicio' => $datos['hora_inicio'],
            ':h_fin' => $datos['hora_fin'],
            ':modalidad' => $datos['modalidad'],
            ':lugar' => $datos['lugar_o_enlace'] ?? null,
            ':estado' => $datos['estado'] ?? 'pendiente',
            ':observaciones' => $datos['observaciones'] ?? null
        ]);
        return $this->conexion->lastInsertId();
    }

    public function editar($id_tutoria, $datos) {
        $sql = "UPDATE {$this->tabla}
                SET id_estudiante = :id_estudiante,
                    id_tutor = :id_tutor,
                    id_materia = :id_materia,
                    fecha = :fecha,
                    hora_inicio = :h_inicio,
                    hora_fin = :h_fin,
                    modalidad = :modalidad,
                    lugar_o_enlace = :lugar,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id_tutoria = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_estudiante' => $datos['id_estudiante'],
            ':id_tutor' => $datos['id_tutor'],
            ':id_materia' => $datos['id_materia'],
            ':fecha' => $datos['fecha'],
            ':h_inicio' => $datos['hora_inicio'],
            ':h_fin' => $datos['hora_fin'],
            ':modalidad' => $datos['modalidad'],
            ':lugar' => $datos['lugar_o_enlace'] ?? null,
            ':estado' => $datos['estado'],
            ':observaciones' => $datos['observaciones'] ?? null,
            ':id' => $id_tutoria
        ]);
        return true;
    }

    public function eliminar($id_tutoria) {
        $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_tutoria = :id");
        $stmt->execute([':id' => $id_tutoria]);
        return true;
    }
}