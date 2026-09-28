<?php
/**
 * Modelo Estudiante — Completo
 */
require_once __DIR__ . '/../config/conexion.php';

class EstudianteModel {
    private $conexion;
    private $tabla = 'estudiantes';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_estudiante) {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_estudiante, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ✅ NUEVO: Obtener por id_usuario
    public function obtenerPorUsuario($id_usuario) {
        $sql = "SELECT e.*, u.nombre, u.apellido, u.telefono, c.nombre_carrera
                FROM {$this->tabla} e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_usuario = :id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editar($id_estudiante, $datos) {
        $this->conexion->beginTransaction();
        try {
            $sql = "UPDATE {$this->tabla}
                    SET id_carrera = :id_carrera, semestre = :semestre, registro_universitario = :registro
                    WHERE id_estudiante = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':id_carrera' => $datos['id_carrera'],
                ':semestre' => $datos['semestre'],
                ':registro' => $datos['registro_universitario'],
                ':id' => $id_estudiante
            ]);

            $sql = "UPDATE usuarios
                    SET nombre = :nombre, apellido = :apellido, telefono = :telefono
                    WHERE id_usuario = :id_usuario";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':apellido' => $datos['apellido'],
                ':telefono' => $datos['telefono'],
                ':id_usuario' => $datos['id_usuario']
            ]);

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    public function eliminar($id_estudiante) {
        $this->conexion->beginTransaction();
        try {
            $stmt = $this->conexion->prepare("SELECT id_usuario FROM {$this->tabla} WHERE id_estudiante = :id");
            $stmt->execute([':id' => $id_estudiante]);
            $est = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$est) throw new Exception("Estudiante no encontrado");

            $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_estudiante = :id");
            $stmt->execute([':id' => $id_estudiante]);

            $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id_usuario");
            $stmt->execute([':id_usuario' => $est['id_usuario']]);

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }
}