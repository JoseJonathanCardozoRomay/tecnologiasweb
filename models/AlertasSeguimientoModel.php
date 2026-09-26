<?php
/**
 * Modelo para Alertas de Seguimiento
 */
require_once __DIR__ . '/../config/conexion.php';

class AlertasSeguimientoModel {
    private $conexion;
    private $tabla = 'alertas_seguimiento';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Listar alertas de un usuario específico
     */
    public function listarPorUsuario($id_usuario) {
        // Ordenamos por id_alerta como respaldo si fecha no existe
        $sql = "SELECT * FROM {$this->tabla} 
                WHERE id_usuario_destino = :id_usuario 
                ORDER BY id_alerta DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marcar alerta como leída
     */
    public function marcarLeida($id_alerta, $id_usuario) {
        $sql = "UPDATE {$this->tabla} 
                SET leida = 1 
                WHERE id_alerta = :id_alerta 
                  AND id_usuario_destino = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_alerta', $id_alerta, PDO::PARAM_INT);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Contar alertas sin leer
     */
    public function contarSinLeer($id_usuario) {
        $sql = "SELECT COUNT(*) FROM {$this->tabla} 
                WHERE id_usuario_destino = :id_usuario AND leida = 0";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}