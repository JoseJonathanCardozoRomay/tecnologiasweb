<?php
/**
 * Modelo — Modalidades de Grado
 * Tabla: modalidades_grado
 */
require_once __DIR__ . '/../config/conexion.php';

class MgModalidadModel {
    private $conexion;
    private $tabla = 'modalidades_grado'; // ✅ Nombre correcto
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas($solo_activas = false) {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY nombre";
        if ($solo_activas) {
            $sql = "SELECT * FROM {$this->tabla} WHERE activa = 1 ORDER BY nombre";
        }
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}