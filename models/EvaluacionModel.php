<?php
/**
 * Modelo Evaluación
 */
require_once __DIR__ . '/../config/conexion.php';
class EvaluacionModel {
    private $conexion;
    private $tabla = 'evaluaciones_tutoria';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function yaExiste($id_tutoria) {
        $sql = "SELECT 1 FROM {$this->tabla} WHERE id_tutoria = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_tutoria]);
        return $stmt->rowCount() > 0;
    }

    public function crear($id_tutoria, $calificacion, $comentario) {
        $sql = "INSERT INTO {$this->tabla} (id_tutoria, calificacion, comentario)
                VALUES (:id_tutoria, :calificacion, :comentario)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutoria', $id_tutoria, PDO::PARAM_INT);
        $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
        $stmt->bindParam(':comentario', $comentario);
        return $stmt->execute();
    }
}