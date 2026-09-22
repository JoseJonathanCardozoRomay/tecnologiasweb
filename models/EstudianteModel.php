<?php
/**
 * Modelo Estudiante
 * Sistema de Gestión de Tutorías
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
        $consulta = "SELECT e.id_estudiante, e.codigo_estudiante, e.fecha_ingreso,
                            u.nombre, u.apellido,
                            c.nombre_carrera
                     FROM {$this->tabla} e
                     INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                     INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT e.*, u.nombre, u.apellido, c.nombre_carrera
                     FROM {$this->tabla} e
                     INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                     INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                     WHERE e.id_estudiante = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (id_usuario, id_carrera, codigo_estudiante, fecha_ingreso)
                         VALUES (:id_usuario, :id_carrera, :codigo_estudiante, :fecha_ingreso)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id_usuario'        => $datos['id_usuario'],
                ':id_carrera'        => $datos['id_carrera'],
                ':codigo_estudiante' => $datos['codigo_estudiante'],
                ':fecha_ingreso'     => $datos['fecha_ingreso']
            ]);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El código del estudiante ya existe'];
            }
            return ['error' => 'Error al registrar el estudiante'];
        }
    }

    public function editar($id, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET
                            id_usuario = :id_usuario,
                            id_carrera = :id_carrera,
                            codigo_estudiante = :codigo_estudiante,
                            fecha_ingreso = :fecha_ingreso
                         WHERE id_estudiante = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id'                => $id,
                ':id_usuario'        => $datos['id_usuario'],
                ':id_carrera'        => $datos['id_carrera'],
                ':codigo_estudiante' => $datos['codigo_estudiante'],
                ':fecha_ingreso'     => $datos['fecha_ingreso']
            ]);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El código del estudiante ya está en uso'];
            }
            return ['error' => 'Error al actualizar el estudiante'];
        }
    }

    public function eliminar($id) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_estudiante = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            return ['error' => 'No se pudo eliminar el estudiante'];
        }
    }

    public function listarUsuariosDisponibles() {
        $consulta = "SELECT u.id_usuario, u.nombre, u.apellido
                     FROM usuarios u
                     LEFT JOIN {$this->tabla} e ON u.id_usuario = e.id_usuario
                     WHERE e.id_usuario IS NULL
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCarreras() {
        $consulta = "SELECT id_carrera, nombre_carrera FROM carreras ORDER BY nombre_carrera";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerUsuarioId($id_estudiante) {
        $consulta = "SELECT id_usuario FROM {$this->tabla} WHERE id_estudiante = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id_estudiante, PDO::PARAM_INT);
        $sentencia->execute();
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        return $resultado['id_usuario'] ?? 0;
    }
}