<?php
/**
 * Modelo para Reportes por Cohorte
 */
require_once __DIR__ . '/../config/conexion.php';

class MgReporteCohorteModel {
    private $conexion;
    private $tabla = 'mg_reportes_cohorte';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY anio_inicio DESC, nombre_cohorte ASC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_reporte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
                (nombre_cohorte, anio_inicio, anio_fin, descripcion, total_estudiantes, estado)
                VALUES (:nombre, :anio_ini, :anio_fin, :desc, :total, :estado)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datos['nombre_cohorte'],
            ':anio_ini' => $datos['anio_inicio'],
            ':anio_fin' => $datos['anio_fin'],
            ':desc' => $datos['descripcion'],
            ':total' => $datos['total_estudiantes'],
            ':estado' => $datos['estado']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                    nombre_cohorte = :nombre,
                    anio_inicio = :anio_ini,
                    anio_fin = :anio_fin,
                    descripcion = :desc,
                    total_estudiantes = :total,
                    estado = :estado
                WHERE id_reporte = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => $datos['nombre_cohorte'],
            ':anio_ini' => $datos['anio_inicio'],
            ':anio_fin' => $datos['anio_fin'],
            ':desc' => $datos['descripcion'],
            ':total' => $datos['total_estudiantes'],
            ':estado' => $datos['estado']
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_reporte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}