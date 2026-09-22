<?php
/**
 * Modelo para la gestión de la entidad Materia
 * Sistema de Gestión de Tutorías
 */
require_once __DIR__ . '/../config/conexion.php';

class MateriaModel {
    private $conexion;
    private $tabla = 'materias';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtiene el listado completo de materias con su carrera
     */
    public function listarTodas() {
        $consulta = "SELECT m.*, c.nombre_carrera
                     FROM {$this->tabla} m
                     LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                     ORDER BY m.nombre_materia ASC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera los datos de una materia por su identificador
     */
    public function obtenerPorId($id_materia) {
        $consulta = "SELECT m.*, c.nombre_carrera
                     FROM {$this->tabla} m
                     LEFT JOIN carreras c ON m.id_carrera = c.id_carrera
                     WHERE m.id_materia = :id_materia";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Registra una nueva materia
     */
    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} 
                (nombre_materia, id_carrera) 
                VALUES (:nombre_materia, :id_carrera)";
            
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute($datos);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'La materia "' . $datos['nombre_materia'] . '" ya se encuentra registrada'];
            }
            return ['error' => 'Error al registrar la materia'];
        }
    }

    /**
     * Modifica los datos de una materia existente
     */
    public function actualizar($id_materia, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET 
                            nombre_materia = :nombre_materia,
                            id_carrera = :id_carrera
                         WHERE id_materia = :id_materia";
            
            $sentencia = $this->conexion->prepare($consulta);
            $datos['id_materia'] = $id_materia;
            $sentencia->execute($datos);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El nombre de materia ingresado ya se encuentra en uso'];
            }
            return ['error' => 'Error al actualizar la información'];
        }
    }

    /**
     * Elimina una materia del sistema
     */
    public function eliminar($id_materia) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_materia = :id_materia";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_materia', $id_materia, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            return ['error' => 'No es posible eliminar: existen registros asociados a esta materia'];
        }
    }

    /**
     * Obtiene el catálogo de carreras para selección
     */
    public function listarCarreras() {
        $consulta = "SELECT * FROM carreras ORDER BY nombre_carrera ASC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}