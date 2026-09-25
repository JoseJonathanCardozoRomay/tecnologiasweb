<?php
/**
 * Modelo para Notificaciones
 * Permisos: Admin=ver todas / Tutor=las suyas / Estudiante=las suyas
 */
require_once __DIR__ . '/../config/conexion.php';

class NotificacionModel {
    private $conexion;
    private $tabla = 'notificaciones';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtener notificaciones de un usuario
     */
    public function listarPorUsuario($id_usuario) {
        $sql = "SELECT * FROM {$this->tabla} 
                WHERE id_usuario = :id_usuario 
                ORDER BY fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar cuántas NO leídas tiene un usuario → para el aviso en el menú
     */
    public function contarNoLeidas($id_usuario) {
        $sql = "SELECT COUNT(*) FROM {$this->tabla} 
                WHERE id_usuario = :id_usuario AND leida = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Marcar como leída
     */
    public function marcarLeida($id_notificacion, $id_usuario) {
        $sql = "UPDATE {$this->tabla} 
                SET leida = 1 
                WHERE id_notificacion = :id AND id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_notificacion, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Marcar TODAS como leídas de un usuario
     */
    public function marcarTodasLeidas($id_usuario) {
        $sql = "UPDATE {$this->tabla} SET leida = 1 WHERE id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Crear notificación
     */
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (id_usuario, tipo, mensaje, url)
                VALUES (:id_usuario, :tipo, :mensaje, :url)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $datos['id_usuario'],
            ':tipo' => $datos['tipo'],
            ':mensaje' => $datos['mensaje'],
            ':url' => $datos['url'] ?? null
        ]);
        return $this->conexion->lastInsertId();
    }

    /**
     * ADMINISTRADOR: ver TODAS las notificaciones del sistema
     */
    public function listarTodas() {
        $sql = "SELECT n.*, u.nombre, u.apellido
                FROM {$this->tabla} n
                LEFT JOIN usuarios u ON n.id_usuario = u.id_usuario
                ORDER BY fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}