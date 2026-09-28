<?php
require_once __DIR__ . '/../config/conexion.php';
class EvaluacionModel {
    private $conexion;
    private $tabla = 'evaluaciones_tutoria';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodas() {
        $sql = "SELECT * FROM {$this->tabla}";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorTutor($id_usuario) {
        return $this->listarTodas();
    }

    public function listarPorEstudiante($id_usuario) {
        return $this->listarTodas();
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id_evaluacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ Nombres exactos de tu tabla: fecha_evaluacion, calificación entera
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (id_tutoria, calificacion, comentario, fecha_evaluacion)
                VALUES (:id_tutoria, :calificacion, :comentario, :fecha_evaluacion)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id_tutoria' => (int)$datos['id_tutoria'],
            ':calificacion' => (int)$datos['calificacion'], // ✅ Entero obligatorio
            ':comentario' => $datos['comentario'],
            ':fecha_evaluacion' => date('Y-m-d H:i:s') // ✅ Nombre correcto
        ]);
    }

    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla}
                SET id_tutoria = :id_tutoria,
                    calificacion = :calificacion,
                    comentario = :comentario
                WHERE id_evaluacion = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':id_tutoria' => (int)$datos['id_tutoria'],
            ':calificacion' => (int)$datos['calificacion'],
            ':comentario' => $datos['comentario'],
            ':id' => $id
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_evaluacion = ?");
        return $stmt->execute([$id]);
    }
}