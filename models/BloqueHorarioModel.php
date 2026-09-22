<?php
require_once __DIR__ . '/../config/conexion.php';

class BloqueHorarioModel {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listar() {
        $sql = "SELECT * FROM bloques_horarios ORDER BY hora_inicio ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_bloque) {
        $sql = "SELECT * FROM bloques_horarios WHERE id_bloque = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_bloque, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre_bloque, $hora_inicio, $hora_fin, $descripcion = null) {
        $sql = "INSERT INTO bloques_horarios (nombre_bloque, hora_inicio, hora_fin, descripcion)
                VALUES (:nombre_bloque, :hora_inicio, :hora_fin, :descripcion)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_bloque', $nombre_bloque);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':descripcion', $descripcion);
        return $stmt->execute();
    }

    public function actualizar($id_bloque, $nombre_bloque, $hora_inicio, $hora_fin, $descripcion = null) {
        $sql = "UPDATE bloques_horarios
                SET nombre_bloque = :nombre_bloque,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    descripcion = :descripcion
                WHERE id_bloque = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':nombre_bloque', $nombre_bloque);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':id', $id_bloque, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id_bloque) {
        $sql = "DELETE FROM bloques_horarios WHERE id_bloque = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_bloque, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>