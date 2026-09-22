 <?php
require_once __DIR__ . '/../config/conexion.php';

class RegistroAccesosModel {
    private $conexion;
    private $tabla = 'registro_accesos';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos() {
        $consulta = "SELECT r.id_acceso, r.fecha_hora, r.ip_origen, r.resultado, r.id_usuario,
                            u.nombre, u.apellido
                     FROM {$this->tabla} r
                     LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
                     ORDER BY r.fecha_hora DESC";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $consulta = "SELECT r.*, u.nombre, u.apellido
                     FROM {$this->tabla} r
                     LEFT JOIN usuarios u ON r.id_usuario = u.id_usuario
                     WHERE r.id_acceso = :id";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
        $sentencia->execute();
        return $sentencia->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        try {
            $consulta = "INSERT INTO {$this->tabla} (id_usuario, ip_origen, resultado)
                         VALUES (:id_usuario, :ip_origen, :resultado)";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id_usuario'   => !empty($datos['id_usuario']) ? $datos['id_usuario'] : null,
                ':ip_origen'    => $datos['ip_origen'],
                ':resultado'    => $datos['resultado']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al registrar el acceso'];
        }
    }

    public function editar($id, $datos) {
        try {
            $consulta = "UPDATE {$this->tabla} SET
                id_usuario = :id_usuario,
                ip_origen  = :ip_origen,
                resultado  = :resultado
                WHERE id_acceso = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->execute([
                ':id'           => $id,
                ':id_usuario'   => !empty($datos['id_usuario']) ? $datos['id_usuario'] : null,
                ':ip_origen'    => $datos['ip_origen'],
                ':resultado'    => $datos['resultado']
            ]);
            return true;
        } catch (PDOException $e) {
            return ['error' => 'Error al actualizar el acceso'];
        }
    }

    public function eliminar($id) {
        try {
            $consulta = "DELETE FROM {$this->tabla} WHERE id_acceso = :id";
            $sentencia = $this->conexion->prepare($consulta);
            $sentencia->bindParam(':id', $id, PDO::PARAM_INT);
            $sentencia->execute();
            return true;
        } catch (PDOException $e) {
            return ['error' => 'No se pudo eliminar'];
        }
    }

    public function listarUsuarios() {
        $consulta = "SELECT id_usuario, nombre, apellido FROM usuarios ORDER BY apellido, nombre";
        $sentencia = $this->conexion->prepare($consulta);
        $sentencia->execute();
        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }
}