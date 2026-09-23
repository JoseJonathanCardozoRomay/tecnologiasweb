<?php
require_once __DIR__ . '/../config/conexion.php';

class TutorModel {
    private $conexion;
    private $tabla = 'tutores';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todos los tutores con datos del usuario
     */
    public function listarTodos() {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.usuario, u.correo
                FROM {$this->tabla} t
                LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY t.id_tutor DESC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtiene un tutor por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.usuario
                FROM {$this->tabla} t
                LEFT JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_tutor = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo tutor
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO {$this->tabla} (id_usuario, especialidad, biografia)
                    VALUES (:id_usuario, :especialidad, :biografia)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':especialidad', $datos['especialidad']);
            $stmt->bindParam(':biografia', $datos['biografia']);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza un tutor
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE {$this->tabla}
                    SET id_usuario = :id_usuario,
                        especialidad = :especialidad,
                        biografia = :biografia
                    WHERE id_tutor = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':especialidad', $datos['especialidad']);
            $stmt->bindParam(':biografia', $datos['biografia']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina un tutor
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM {$this->tabla} WHERE id_tutor = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}