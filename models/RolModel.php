 <?php
/**
 * Modelo para la gestión de la entidad Rol
 * Maneja las operaciones de acceso a datos
 */

require_once __DIR__ . '/../config/conexion.php';

class RolModel {
    private $conexion;
    private $tabla = 'roles';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $consulta = "SELECT * FROM {$this->tabla} ORDER BY id_rol";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_rol) {
        $consulta = "SELECT * FROM {$this->tabla} WHERE id_rol = :id_rol";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre_rol) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (nombre_rol) VALUES (:nombre_rol)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre_rol', $nombre_rol, PDO::PARAM_STR);
            return $sentencia->execute();
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El rol "' . $nombre_rol . '" ya se encuentra registrado'];
            }
            return ['error' => 'Error al crear el registro'];
        }
    }

    public function actualizar($id_rol, $nombre_rol) {
        try {
            $consulta = "UPDATE {$this->tabla} SET nombre_rol = :nombre_rol WHERE id_rol = :id_rol";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':nombre_rol', $nombre_rol, PDO::PARAM_STR);
            $sentencia->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            if ($error->getCode() === '23000') {
                return ['error' => 'El rol "' . $nombre_rol . '" ya se encuentra registrado'];
            }
            return ['error' => 'Error al actualizar el registro'];
        }
    }

    public function eliminar($id_rol) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_rol = :id_rol";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            return $sentencia->execute();
        } catch (PDOException $error) {
            return ['error' => 'No es posible eliminar: existen dependencias'];
        }
    }
}