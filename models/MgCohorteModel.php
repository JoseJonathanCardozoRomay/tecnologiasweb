<?php
/**
 * Modelo — Cohortes
 * Tabla: cohortes_mg
 */
require_once __DIR__ . '/../config/conexion.php';

class MgCohorteModel {
    private $conexion;
    private $tabla = 'cohortes_mg'; // ✅ Nombre correcto
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY fecha_inicio DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_cohorte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (codigo, nombre, fecha_inicio, fecha_fin, activa)
                VALUES (:codigo, :nombre, :fecha_inicio, :fecha_fin, :activa)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
        $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
        $stmt->bindParam(':activa', $datos['activa']);
        return $stmt->execute();
    }

    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                    codigo = :codigo,
                    nombre = :nombre,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    activa = :activa
                WHERE id_cohorte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':codigo', $datos['codigo']);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':fecha_inicio', $datos['fecha_inicio']);
        $stmt->bindParam(':fecha_fin', $datos['fecha_fin']);
        $stmt->bindParam(':activa', $datos['activa']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}