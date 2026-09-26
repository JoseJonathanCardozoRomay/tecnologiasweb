<?php
/**
 * Modelo para Tribunales / Jurados
 */
require_once __DIR__ . '/../config/conexion.php';

class MgTribunalModel {
    private $conexion;
    private $tabla = 'mg_tribunales';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos($solo_activos = false) {
        $sql = "SELECT * FROM {$this->tabla}";
        if ($solo_activos) $sql .= " WHERE activo = 1";
        $sql .= " ORDER BY nombre_completo ASC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_tribunal = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
                (nombre_completo, especialidad, correo, telefono, activo)
                VALUES (:nc, :esp, :cor, :tel, :act)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':nc' => $datos['nombre_completo'],
            ':esp' => $datos['especialidad'],
            ':cor' => $datos['correo'],
            ':tel' => $datos['telefono'],
            ':act' => $datos['activo']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE {$this->tabla} SET
                    nombre_completo = :nc, especialidad = :esp,
                    correo = :cor, telefono = :tel, activo = :act
                WHERE id_tribunal = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nc' => $datos['nombre_completo'],
            ':esp' => $datos['especialidad'],
            ':cor' => $datos['correo'],
            ':tel' => $datos['telefono'],
            ':act' => $datos['activo']
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_tribunal = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}