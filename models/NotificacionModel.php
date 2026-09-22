<?php
require_once __DIR__ . '/../config/conexion.php';

class NotificacionModel {
    private $conexion;
    private $tabla = 'notificaciones';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT n.*, u.nombre 
                FROM {$this->tabla} n
                LEFT JOIN usuarios u ON n.id_usuario = u.id_usuario
                ORDER BY n.fecha_creacion DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_notificacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (id_usuario, tipo, mensaje, url)
                VALUES (:idusu, :tipo, :mensaje, :url)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idusu', $datos['id_usuario']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':mensaje', $datos['mensaje']);
        $stmt->bindParam(':url', $datos['url']);
        return $stmt->execute();
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE {$this->tabla}
                SET id_usuario = :idusu, tipo = :tipo, mensaje = :mensaje, url = :url, leida = :leida
                WHERE id_notificacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':idusu', $datos['id_usuario']);
        $stmt->bindParam(':tipo', $datos['tipo']);
        $stmt->bindParam(':mensaje', $datos['mensaje']);
        $stmt->bindParam(':url', $datos['url']);
        $stmt->bindParam(':leida', $datos['leida']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_notificacion = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}