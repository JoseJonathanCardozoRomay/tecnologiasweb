<?php
/**
 * Modelo para la gestión de la entidad Usuario
 */
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel {
    private $conexion;
    private $tabla = 'usuarios';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todos los usuarios con nombre del rol
     */
    public function listarTodos() {
        $consulta = "SELECT u.*, r.nombre_rol 
                     FROM {$this->tabla} u 
                     LEFT JOIN roles r ON u.id_rol = r.id_rol 
                     ORDER BY u.id_usuario";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un usuario por su ID
     */
    public function obtenerPorId($id_usuario) {
        $consulta = "SELECT * FROM {$this->tabla} WHERE id_usuario = :id_usuario";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo usuario
     */
    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} 
                (id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono, estado) 
                VALUES (:id_rol, :nombre, :apellido, :correo, :usuario, :contrasena_hash, :telefono, :estado)";
            
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute($datos);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El correo o nombre de usuario ya están registrados'];
            }
            return ['error' => 'Error al crear el registro: ' . $error->getMessage()];
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function actualizar($id_usuario, $datos) {
        try {
            $campos = [];
            $valores = [];

            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $valores[$campo] = $valor;
            }
            $valores['id_usuario'] = $id_usuario;

            $consulta = "UPDATE {$this->tabla} SET " . implode(', ', $campos) . " 
                         WHERE id_usuario = :id_usuario";
            
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute($valores);
            return true;
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El correo o nombre de usuario ya están registrados'];
            }
            return ['error' => 'Error al actualizar el registro'];
        }
    }

    /**
     * Elimina un usuario
     */
    public function eliminar($id_usuario) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_usuario = :id_usuario";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $error) {
            return ['error' => 'No es posible eliminar el registro: existen dependencias'];
        }
    }

    /**
     * Obtiene lista de roles para el formulario
     */
    public function listarRoles() {
        $consulta = "SELECT * FROM roles ORDER BY id_rol";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}