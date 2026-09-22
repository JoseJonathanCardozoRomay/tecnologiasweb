<?php
require_once __DIR__ . '/../config/conexion.php';

class SeguimientoSesionModel {
    private $conexion;
    private $tabla = 'seguimiento_sesion';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT s.*, t.fecha, t.estado AS estado_tutoria
                FROM {$this->tabla} s
                LEFT JOIN tutorias t ON s.id_tutoria = t.id_tutoria
                ORDER BY s.fecha_registro DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId($id) {
        $sql = "SELECT s.*, t.fecha, t.estado AS estado_tutoria
                FROM {$this->tabla} s
                LEFT JOIN tutorias t ON s.id_tutoria = t.id_tutoria
                WHERE s.id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarTutoriasDisponibles() {
        $sql = "SELECT id_tutoria, fecha, estado FROM tutorias ORDER BY fecha DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear($datos) {
        try {
            $sql = "INSERT INTO {$this->tabla} (id_tutoria, asistio, temas_tratados, avance, recomendaciones)
                    VALUES (:idtut, :asistio, :temas, :avance, :recomend)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idtut', $datos['id_tutoria'], PDO::PARAM_INT);
            $stmt->bindParam(':asistio', $datos['asistio']);
            $stmt->bindParam(':temas', $datos['temas_tratados']);
            $stmt->bindParam(':avance', $datos['avance']);
            $stmt->bindParam(':recomend', $datos['recomendaciones']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla}
                    SET id_tutoria = :idtut,
                        asistio = :asistio,
                        temas_tratados = :temas,
                        avance = :avance,
                        recomendaciones = :recomend
                    WHERE id_seguimiento = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':idtut', $datos['id_tutoria'], PDO::PARAM_INT);
            $stmt->bindParam(':asistio', $datos['asistio']);
            $stmt->bindParam(':temas', $datos['temas_tratados']);
            $stmt->bindParam(':avance', $datos['avance']);
            $stmt->bindParam(':recomend', $datos['recomendaciones']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_seguimiento = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}