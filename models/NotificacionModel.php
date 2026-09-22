<?php
require_once __DIR__ . '/../config/conexion.php';

class NotificacionModel {
    private $conexion;

    public function __construct() {
        $this->conexion = Conexion::conectar();
    }

    public function listar($id_usuario = null) {
        $sql = "SELECT n.*, u.nombre, u.apellido 
                FROM notificaciones n 
                LEFT JOIN usuarios u ON n.id_usuario = u.id_usuario";
        if ($id_usuario) {
            $sql .= " WHERE n.id_usuario = ? ORDER BY n.fecha_creacion DESC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_usuario);
        } else {
            $sql .= " ORDER BY n.fecha_creacion DESC";
            $stmt = $this->conexion->prepare($sql);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($id_usuario, $tipo, $mensaje, $url = null) {
        $stmt = $this->conexion->prepare("INSERT INTO notificaciones (id_usuario, tipo, mensaje, url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_usuario, $tipo, $mensaje, $url);
        return $stmt->execute();
    }

    public function obtenerPorId($id_notificacion) {
        $stmt = $this->conexion->prepare("SELECT * FROM notificaciones WHERE id_notificacion = ?");
        $stmt->bind_param("i", $id_notificacion);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function marcarLeida($id_notificacion) {
        $stmt = $this->conexion->prepare("UPDATE notificaciones SET leida = 1 WHERE id_notificacion = ?");
        $stmt->bind_param("i", $id_notificacion);
        return $stmt->execute();
    }

    public function eliminar($id_notificacion) {
        $stmt = $this->conexion->prepare("DELETE FROM notificaciones WHERE id_notificacion = ?");
        $stmt->bind_param("i", $id_notificacion);
        return $stmt->execute();
    }
}
?>