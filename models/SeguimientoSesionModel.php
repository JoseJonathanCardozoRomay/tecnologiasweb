<?php
require_once __DIR__ . '/../config/conexion.php';

class SeguimientoSesionModel {
    private $conexion;
    private $tabla = 'seguimiento_sesion';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Tutor: solo sus seguimientos
     */
    public function listarPorTutor($id_usuario_tutor) {
        $sql = "SELECT s.*, 
                       tu.fecha, tu.hora_inicio, tu.hora_fin,
                       CONCAT(e_nombre.nombre, ' ', e_nombre.apellido) AS estudiante_nombre,
                       CONCAT(t_nombre.nombre, ' ', t_nombre.apellido) AS tutor_nombre,
                       m.nombre_materia
                FROM {$this->tabla} s
                INNER JOIN tutorias tu ON s.id_tutoria = tu.id_tutoria
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios e_nombre ON e.id_usuario = e_nombre.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios t_nombre ON t.id_usuario = t_nombre.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                WHERE t.id_usuario = :id_usuario
                ORDER BY s.fecha_registro DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Estudiante: solo sus seguimientos
     */
    public function listarPorEstudiante($id_usuario_estudiante) {
        $sql = "SELECT s.*, 
                       tu.fecha, tu.hora_inicio, tu.hora_fin,
                       CONCAT(e_nombre.nombre, ' ', e_nombre.apellido) AS estudiante_nombre,
                       CONCAT(t_nombre.nombre, ' ', t_nombre.apellido) AS tutor_nombre,
                       m.nombre_materia
                FROM {$this->tabla} s
                INNER JOIN tutorias tu ON s.id_tutoria = tu.id_tutoria
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios e_nombre ON e.id_usuario = e_nombre.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios t_nombre ON t.id_usuario = t_nombre.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                WHERE e.id_usuario = :id_usuario
                ORDER BY s.fecha_registro DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario_estudiante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Administrador: todos
     */
    public function listarTodos() {
        $sql = "SELECT s.*, 
                       tu.fecha, tu.hora_inicio, tu.hora_fin,
                       CONCAT(e_nombre.nombre, ' ', e_nombre.apellido) AS estudiante_nombre,
                       CONCAT(t_nombre.nombre, ' ', t_nombre.apellido) AS tutor_nombre,
                       m.nombre_materia
                FROM {$this->tabla} s
                INNER JOIN tutorias tu ON s.id_tutoria = tu.id_tutoria
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios e_nombre ON e.id_usuario = e_nombre.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios t_nombre ON t.id_usuario = t_nombre.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                ORDER BY s.fecha_registro DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT s.*, 
                       tu.fecha, tu.hora_inicio, tu.hora_fin,
                       CONCAT(e_nombre.nombre, ' ', e_nombre.apellido) AS estudiante_nombre,
                       CONCAT(t_nombre.nombre, ' ', t_nombre.apellido) AS tutor_nombre,
                       t_nombre.id_usuario AS id_usuario_tutor,
                       e_nombre.id_usuario AS id_usuario_estudiante,
                       m.nombre_materia
                FROM {$this->tabla} s
                INNER JOIN tutorias tu ON s.id_tutoria = tu.id_tutoria
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios e_nombre ON e.id_usuario = e_nombre.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN usuarios t_nombre ON t.id_usuario = t_nombre.id_usuario
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                WHERE s.id_seguimiento = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Listar tutorías asignadas al Tutor (para crear seguimiento)
     */
    public function listarTutoriasParaTutor($id_usuario_tutor) {
        $sql = "SELECT DISTINCT tu.id_tutoria, tu.fecha, tu.hora_inicio,
                       CONCAT(e_nombre.nombre, ' ', e_nombre.apellido) AS estudiante_nombre,
                       m.nombre_materia
                FROM tutorias tu
                INNER JOIN estudiantes e ON tu.id_estudiante = e.id_estudiante
                INNER JOIN usuarios e_nombre ON e.id_usuario = e_nombre.id_usuario
                INNER JOIN tutores t ON tu.id_tutor = t.id_tutor
                INNER JOIN materias m ON tu.id_materia = m.id_materia
                WHERE t.id_usuario = :id_usuario
                ORDER BY tu.fecha DESC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario_tutor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Crear
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (id_tutoria, asistio, temas_tratados, avance, recomendaciones)
                VALUES (:id_tutoria, :asistio, :temas_tratados, :avance, :recomendaciones)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $datos['id_tutoria'], PDO::PARAM_INT);
        $stmt->bindParam(':asistio', $datos['asistio']);
        $temas = $datos['temas_tratados'] ?? '';
        $stmt->bindParam(':temas_tratados', $temas);
        $avance = $datos['avance'] ?? 'sin_avance';
        $stmt->bindParam(':avance', $avance);
        $recomendaciones = $datos['recomendaciones'] ?? '';
        $stmt->bindParam(':recomendaciones', $recomendaciones);
        return $stmt->execute();
    }

    /**
     * Editar
     */
    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                    id_tutoria = :id_tutoria,
                    asistio = :asistio,
                    temas_tratados = :temas_tratados,
                    avance = :avance,
                    recomendaciones = :recomendaciones
                WHERE id_seguimiento = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $datos['id_tutoria'], PDO::PARAM_INT);
        $stmt->bindParam(':asistio', $datos['asistio']);
        $stmt->bindParam(':temas_tratados', $datos['temas_tratados'] ?? '');
        $stmt->bindParam(':avance', $datos['avance'] ?? 'sin_avance');
        $stmt->bindParam(':recomendaciones', $datos['recomendaciones'] ?? '');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Eliminar
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}