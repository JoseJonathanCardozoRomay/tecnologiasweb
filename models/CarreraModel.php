<?php
/**
 * Modelo para la gestión de la entidad Carrera
 * Sistema de Gestión de Tutorías
 */
require_once __DIR__ . '/../config/conexion.php';

class CarreraModel {
    private $conexion;
    private $tabla = 'carreras';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtiene el listado completo de carreras ordenadas alfabéticamente
     * @return array Lista de carreras
     */
    public function listarTodas() {
        $consulta = "SELECT * FROM {$this->tabla} ORDER BY nombre_carrera ASC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera los datos de una carrera por su identificador
     * @param int $id_carrera Identificador de la carrera
     * @return array|false Datos de la carrera
     */
    public function obtenerPorId($id_carrera) {
        $consulta = "SELECT * FROM {$this->tabla} WHERE id_carrera = :id_carrera";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_carrera', $id_carrera, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una nueva carrera en la base de datos
     * @param string $nombre_carrera Nombre de la carrera
     * @return bool|array Resultado de la operación
     */
    public function crear($nombre_carrera) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (nombre_carrera) VALUES (:nombre_carrera)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre_carrera', $nombre_carrera, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'La carrera "' . $nombre_carrera . '" ya se encuentra registrada'];
            }
            return ['error' => 'Error al registrar la carrera'];
        }
    }

    /**
     * Modifica el nombre de una carrera existente
     * @param int $id_carrera Identificador de la carrera
     * @param string $nombre_carrera Nuevo nombre
     * @return bool|array Resultado de la operación
     */
    public function actualizar($id_carrera, $nombre_carrera) {
        try {
            $consulta = "UPDATE {$this->tabla} SET nombre_carrera = :nombre_carrera WHERE id_carrera = :id_carrera";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre_carrera', $nombre_carrera, PDO::PARAM_STR);
            $sentencia->bindParam(':id_carrera', $id_carrera, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'La carrera "' . $nombre_carrera . '" ya se encuentra registrada'];
            }
            return ['error' => 'Error al actualizar el registro'];
        }
    }

    /**
     * Elimina una carrera del sistema
     * @param int $id_carrera Identificador de la carrera
     * @return bool|array Resultado de la operación
     */
    public function eliminar($id_carrera) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_carrera = :id_carrera";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_carrera', $id_carrera, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return ['error' => 'No es posible eliminar: existen registros asociados a esta carrera'];
        }
    }
}