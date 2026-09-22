<?php
/**
 * Modelo Disponibilidad de Tutor
 * Sistema de Gestión de Tutorías
 */
require_once __DIR__ . '/../config/conexion.php';

class DisponibilidadTutorModel {
    private $conexion;
    private $tabla = 'disponibilidad_tutor';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $consulta = "SELECT d.id_disponibilidad, d.dia_semana, d.hora_inicio, d.hora_fin,
                            u.nombre, u.apellido
                     FROM {$this->tabla} d
                     INNER JOIN tutores t ON d.id_tutor = t.id_tutor
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     ORDER BY FIELD(d.dia_semana, 'Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'), d.hora_inicio";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT d.*, u.nombre, u.apellido
                     FROM {$this->tabla} d
                     INNER JOIN tutores t ON d.id_tutor = t.id_tutor
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     WHERE d.id_disponibilidad = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (id_tutor, dia_semana, hora_inicio, hora_fin)
                         VALUES (:id_tutor, :dia_semana, :hora_inicio, :hora_fin)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id_tutor'     => $datos['id_tutor'],
                ':dia_semana'   => $datos['dia_semana'],
                ':hora_inicio'  => $datos['hora_inicio'],
                ':hora_fin'     => $datos['hora_fin']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al registrar disponibilidad'];
        }
    }

    public function editar($id, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET
                            id_tutor = :id_tutor,
                            dia_semana = :dia_semana,
                            hora_inicio = :hora_inicio,
                            hora_fin = :hora_fin
                         WHERE id_disponibilidad = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id'           => $id,
                ':id_tutor'     => $datos['id_tutor'],
                ':dia_semana'   => $datos['dia_semana'],
                ':hora_inicio'  => $datos['hora_inicio'],
                ':hora_fin'     => $datos['hora_fin']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al actualizar disponibilidad'];
        }
    }

    public function eliminar($id) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_disponibilidad = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $e) {
            return ['error' => 'No se pudo eliminar'];
        }
    }

    public function listarTutores() {
        $consulta = "SELECT t.id_tutor, u.nombre, u.apellido
                     FROM tutores t
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}