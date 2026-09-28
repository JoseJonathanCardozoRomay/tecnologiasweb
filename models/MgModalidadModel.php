<?php
/**
 * Modelo Modalidades de Grado
 */
require_once __DIR__ . '/../config/conexion.php';

class MgModalidadModel {
    private $conexion;
    private $tabla = 'mg_modalidades';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    // ✅ Ambos nombres para que no falle: listarTodos y listarTodas
    public function listarTodos() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY nombre ASC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas() {
        return $this->listarTodos();
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->tabla} WHERE id_modalidad = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}