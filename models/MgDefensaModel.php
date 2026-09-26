<?php
/**
 * Modelo para Programación de Defensas
 * Adaptado EXACTAMENTE a los nombres de tu tabla
 */
require_once __DIR__ . '/../config/conexion.php';

class MgDefensaModel {
    private $conexion;
    private $tabla = 'mg_defensas';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT d.*, t.nombre_completo AS nombre_tribunal
                FROM {$this->tabla} d
                LEFT JOIN mg_tribunales t ON d.id_tribunal = t.id_tribunal
                ORDER BY d.fecha_defensa DESC, d.hora_defensa ASC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT d.*, t.nombre_completo AS nombre_tribunal
                FROM {$this->tabla} d
                LEFT JOIN mg_tribunales t ON d.id_tribunal = t.id_tribunal
                WHERE d.id_defensa = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
                (id_tribunal, id_expediente, fecha_defensa, hora_defensa, lugar, estado_defensa, observaciones_programacion)
                VALUES (:idt, :exp, :fec, :hd, :lug, :est, :obs)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':idt' => $datos['id_tribunal'],
            ':exp' => $datos['id_expediente'],
            ':fec' => $datos['fecha_defensa'],
            ':hd'  => $datos['hora_defensa'],
            ':lug' => $datos['lugar'],
            ':est' => $datos['estado_defensa'],
            ':obs' => $datos['observaciones_programacion']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                    id_tribunal              = :idt,
                    id_expediente            = :exp,
                    fecha_defensa            = :fec,
                    hora_defensa             = :hd,
                    lugar                    = :lug,
                    estado_defensa           = :est,
                    observaciones_programacion = :obs
                WHERE id_defensa = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id'  => $id,
            ':idt' => $datos['id_tribunal'],
            ':exp' => $datos['id_expediente'],
            ':fec' => $datos['fecha_defensa'],
            ':hd'  => $datos['hora_defensa'],
            ':lug' => $datos['lugar'],
            ':est' => $datos['estado_defensa'],
            ':obs' => $datos['observaciones_programacion']
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_defensa = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}