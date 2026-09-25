<?php
/**
 * Modelo Tutor
 */
require_once __DIR__ . '/../config/conexion.php';

class TutorModel {
    private $conexion;
    private $tabla = 'tutores';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.usuario, u.correo
                FROM {$this->tabla} t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                ORDER BY u.nombre, u.apellido";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($id_usuario, $especialidad, $biografia) {
        $sql = "INSERT INTO {$this->tabla} (id_usuario, especialidad, biografia) 
                VALUES (:id_usuario, :especialidad, :biografia)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->bindParam(':especialidad', $especialidad);
        $stmt->bindParam(':biografia', $biografia);
        return $stmt->execute();
    }

    // Agrega aquí los demás métodos: obtenerPorId, actualizar, eliminar
    public function obtenerPorId($id_tutor) {
        $sql = "SELECT t.*, u.nombre, u.apellido, u.usuario, u.correo
                FROM {$this->tabla} t
                INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                WHERE t.id_tutor = :id_tutor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id_tutor, $especialidad, $biografia) {
        $sql = "UPDATE {$this->tabla} 
                SET especialidad = :especialidad, biografia = :biografia 
                WHERE id_tutor = :id_tutor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':especialidad', $especialidad);
        $stmt->bindParam(':biografia', $biografia);
        $stmt->bindParam(':id_tutor', $id_tutor);
        return $stmt->execute();
    }

    public function eliminar($id_tutor) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_tutor = :id_tutor";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_tutor', $id_tutor);
        return $stmt->execute();
    }
}