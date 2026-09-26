<?php
require_once __DIR__ . '/../config/conexion.php';
class ReunionSeguimientoModel {
    private $conexion;
    private $tabla = 'reuniones_seguimiento';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarPorTutoria($id_tutoria) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_tutoria = :id_tutoria ORDER BY fecha_reunion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $id_tutoria);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_reunion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
            (id_tutoria, titulo, fecha_reunion, ubicacion, enlace, observaciones_inicio, asistencia, id_usuario_registra)
            VALUES (:id_tutoria, :titulo, :fecha_reunion, :ubicacion, :enlace, :observaciones_inicio, 'pendiente', :id_usuario_registra)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_tutoria' => $datos['id_tutoria'],
            ':titulo' => $datos['titulo'],
            ':fecha_reunion' => $datos['fecha_reunion'],
            ':ubicacion' => $datos['ubicacion'],
            ':enlace' => $datos['enlace'],
            ':observaciones_inicio' => $datos['observaciones_inicio'],
            ':id_usuario_registra' => $datos['id_usuario_registra']
        ]);
        return $this->conexion->lastInsertId();
    }

    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
            titulo = :titulo,
            fecha_reunion = :fecha_reunion,
            ubicacion = :ubicacion,
            enlace = :enlace,
            asistencia = :asistencia,
            observaciones_inicio = :observaciones_inicio
            WHERE id_reunion = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':fecha_reunion' => $datos['fecha_reunion'],
            ':ubicacion' => $datos['ubicacion'],
            ':enlace' => $datos['enlace'],
            ':asistencia' => $datos['asistencia'],
            ':observaciones_inicio' => $datos['observaciones_inicio'],
            ':id' => $id
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_reunion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}