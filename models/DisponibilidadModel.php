<?php
require_once __DIR__ . '/../config/conexion.php';

class DisponibilidadModel {
    private $conexion;
    private $tabla = 'disponibilidad_tutor';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT d.*, t.nombre, t.apellido
                FROM {$this->tabla} d
                INNER JOIN tutores tut ON d.id_tutor = tut.id_tutor
                INNER JOIN usuarios t ON tut.id_usuario = t.id_usuario
                ORDER BY d.dia_semana, d.hora_inicio";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ✅ Obtener por ID
    public function obtenerPorId($id) {
        $sql = "SELECT d.*, t.nombre, t.apellido
                FROM {$this->tabla} d
                INNER JOIN tutores tut ON d.id_tutor = tut.id_tutor
                INNER JOIN usuarios t ON tut.id_usuario = t.id_usuario
                WHERE d.id_disponibilidad = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Actualizar
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla} 
                    SET id_tutor = :id_tutor,
                        dia_semana = :dia,
                        hora_inicio = :hinicio,
                        hora_fin = :hfin
                    WHERE id_disponibilidad = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_tutor', $datos['id_tutor']);
            $stmt->bindParam(':dia', $datos['dia_semana']);
            $stmt->bindParam(':hinicio', $datos['hora_inicio']);
            $stmt->bindParam(':hfin', $datos['hora_fin']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}