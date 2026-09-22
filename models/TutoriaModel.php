<?php
/**
 * Modelo Sesiones de Tutoría — CORREGIDO
 * Sistema de Gestión de Tutorías
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
        $consulta = "SELECT t.id_tutoria, t.fecha, t.hora_inicio, t.hora_fin, t.modalidad, t.estado,
                            e.codigo_estudiante,
                            ue.nombre AS nom_est, ue.apellido AS ape_est,
                            ut.nombre AS nom_tut, ut.apellido AS ape_tut,
                            m.nombre_materia
                     FROM {$this->tabla} t
                     INNER JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                     INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                     INNER JOIN tutores tut ON t.id_tutor = tut.id_tutor
                     INNER JOIN usuarios ut ON tut.id_usuario = ut.id_usuario
                     INNER JOIN materias m ON t.id_materia = m.id_materia
                     ORDER BY t.fecha DESC, t.hora_inicio DESC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT t.*,
                            ue.nombre AS nom_est, ue.apellido AS ape_est,
                            ut.nombre AS nom_tut, ut.apellido AS ape_tut,
                            m.nombre_materia
                     FROM {$this->tabla} t
                     INNER JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                     INNER JOIN usuarios ue ON e.id_usuario = ue.id_usuario
                     INNER JOIN tutores tut ON t.id_tutor = tut.id_tutor
                     INNER JOIN usuarios ut ON tut.id_usuario = ut.id_usuario
                     INNER JOIN materias m ON t.id_materia = m.id_materia
                     WHERE t.id_tutoria = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} 
                (id_estudiante, id_tutor, id_materia, fecha, hora_inicio, hora_fin, modalidad, lugar_o_enlace, estado, observaciones)
                VALUES (:id_estudiante, :id_tutor, :id_materia, :fecha, :hora_inicio, :hora_fin, :modalidad, :lugar_o_enlace, :estado, :observaciones)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id_estudiante'   => $datos['id_estudiante'],
                ':id_tutor'        => $datos['id_tutor'],
                ':id_materia'      => $datos['id_materia'],
                ':fecha'           => $datos['fecha'],
                ':hora_inicio'     => $datos['hora_inicio'],
                ':hora_fin'        => $datos['hora_fin'],
                ':modalidad'       => $datos['modalidad'],
                ':lugar_o_enlace'  => $datos['lugar_o_enlace'],
                ':estado'          => $datos['estado'],
                ':observaciones'   => $datos['observaciones']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al registrar la tutoría'];
        }
    }

    public function editar($id, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET
                id_estudiante   = :id_estudiante,
                id_tutor        = :id_tutor,
                id_materia      = :id_materia,
                fecha           = :fecha,
                hora_inicio     = :hora_inicio,
                hora_fin        = :hora_fin,
                modalidad       = :modalidad,
                lugar_o_enlace  = :lugar_o_enlace,
                estado          = :estado,
                observaciones   = :observaciones
                WHERE id_tutoria = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id'              => $id,
                ':id_estudiante'   => $datos['id_estudiante'],
                ':id_tutor'        => $datos['id_tutor'],
                ':id_materia'      => $datos['id_materia'],
                ':fecha'           => $datos['fecha'],
                ':hora_inicio'     => $datos['hora_inicio'],
                ':hora_fin'        => $datos['hora_fin'],
                ':modalidad'       => $datos['modalidad'],
                ':lugar_o_enlace'  => $datos['lugar_o_enlace'],
                ':estado'          => $datos['estado'],
                ':observaciones'   => $datos['observaciones']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al actualizar la tutoría'];
        }
    }

    public function eliminar($id) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_tutoria = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $e) {
            return ['error' => 'No se pudo eliminar'];
        }
    }

    public function listarEstudiantes() {
        $consulta = "SELECT e.id_estudiante, u.nombre, u.apellido, e.codigo_estudiante
                     FROM estudiantes e
                     INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTutores() {
        $consulta = "SELECT t.id_tutor, u.nombre, u.apellido
                     FROM tutores t
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarMaterias() {
        $consulta = "SELECT id_materia, nombre_materia FROM materias ORDER BY nombre_materia";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}