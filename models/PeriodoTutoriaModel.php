<?php
require_once __DIR__ . '/../config/conexion.php';

class PeriodoTutoriaModel {
    private $conexion;
    private $tabla = 'periodos_tutoria';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY fecha_inicio DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        try {
            // Verificar si el código ya existe
            $chk = $this->conexion->prepare("SELECT 1 FROM {$this->tabla} WHERE codigo = :codigo LIMIT 1");
            $chk->bindParam(':codigo', $datos['codigo']);
            $chk->execute();
            
            if ($chk->fetch()) {
                return 'existe'; // Código repetido
            }

            $sql = "INSERT INTO {$this->tabla} (codigo, nombre, fecha_inicio, fecha_fin, activo, creado_por)
                    VALUES (:codigo, :nombre, :finicio, :ffin, :activo, :creado_por)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo', $datos['codigo']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':finicio', $datos['fecha_inicio']);
            $stmt->bindParam(':ffin', $datos['fecha_fin']);
            $stmt->bindParam(':activo', $datos['activo']);
            $stmt->bindParam(':creado_por', $datos['creado_por']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_periodo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla} 
                    SET codigo = :codigo,
                        nombre = :nombre,
                        fecha_inicio = :finicio,
                        fecha_fin = :ffin,
                        activo = :activo
                    WHERE id_periodo = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':codigo', $datos['codigo']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':finicio', $datos['fecha_inicio']);
            $stmt->bindParam(':ffin', $datos['fecha_fin']);
            $stmt->bindParam(':activo', $datos['activo']);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                return 'existe';
            }
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_periodo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}