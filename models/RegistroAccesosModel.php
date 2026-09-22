<?php
require_once __DIR__ . '/../config/conexion.php';

class RegistroAccesosModel {
    private $conexion;
    private $tabla = 'registro_accesos';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT ra.*, u.nombre, u.apellido, u.usuario
                FROM {$this->tabla} ra
                LEFT JOIN usuarios u ON ra.id_usuario = u.id_usuario
                ORDER BY ra.fecha_hora DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_acceso = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $sql = "INSERT INTO {$this->tabla} (id_usuario, ip_origen, resultado)
                    VALUES (:idusu, :ip, :res)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idusu', $datos['id_usuario'], $datos['id_usuario'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':ip', $datos['ip_origen']);
            $stmt->bindParam(':res', $datos['resultado']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla}
                    SET id_usuario = :idusu,
                        ip_origen = :ip,
                        resultado = :res
                    WHERE id_acceso = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':idusu', $datos['id_usuario'], $datos['id_usuario'] ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindParam(':ip', $datos['ip_origen']);
            $stmt->bindParam(':res', $datos['resultado']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_acceso = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}