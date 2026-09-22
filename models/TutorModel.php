<?php
/**
 * Modelo para la gestión de la entidad Tutor
 * Sistema de Gestión de Tutorías
 */
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
        $consulta = "SELECT t.*, u.nombre, u.apellido, u.correo, u.usuario
                     FROM {$this->tabla} t
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un tutor por su ID
     */
    public function obtenerPorId($id_tutor) {
        $consulta = "SELECT t.*, u.nombre, u.apellido, u.correo, u.usuario
                     FROM {$this->tabla} t
                     INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                     WHERE t.id_tutor = :id_tutor";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registra un nuevo tutor
     */
    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} 
                (id_usuario, especialidad, biografia) 
                VALUES (:id_usuario, :especialidad, :biografia)";
            
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute($datos);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El usuario seleccionado ya se encuentra registrado como tutor'];
            }
            return ['error' => 'Error al registrar: ' . $error->getMessage()];
        }
    }

    /**
     * Actualiza la información de un tutor
     */
    public function actualizar($id_tutor, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET 
                            especialidad = :especialidad,
                            biografia = :biografia
                         WHERE id_tutor = :id_tutor";
            
            $sentencia = $this->conexion->prepare($consulta);
            $datos['id_tutor'] = $id_tutor;
            $sentencia->execute($datos);
            return true;
        } catch (PDOException $error) {
            return ['error' => 'Error al actualizar la información'];
        }
    }

    /**
     * Elimina un tutor
     */
    public function eliminar($id_tutor) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_tutor = :id_tutor";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_tutor', $id_tutor, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            return ['error' => 'No es posible eliminar el registro: existen dependencias'];
        }
    }

    /**
     * Obtiene usuarios disponibles para asignar como tutores
     * (excluye los que ya son tutores)
     */
    public function listarUsuariosDisponibles() {
        $consulta = "SELECT u.id_usuario, u.nombre, u.apellido, u.usuario
                     FROM usuarios u
                     LEFT JOIN {$this->tabla} t ON u.id_usuario = t.id_usuario
                     WHERE t.id_usuario IS NULL
                     ORDER BY u.apellido, u.nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los usuarios (para edición)
     */
    public function listarTodosUsuarios() {
        $consulta = "SELECT * FROM usuarios ORDER BY apellido, nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}