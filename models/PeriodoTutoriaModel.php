 <?php
require_once __DIR__ . '/../config/conexion.php';

class PeriodoTutoriaModel {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listar() {
        $sql = "SELECT p.*, u.nombre AS nombre_usuario, u.apellido AS apellido_usuario
                FROM periodos_tutoria p
                LEFT JOIN usuarios u ON p.creado_por = u.id_usuario
                ORDER BY p.fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_periodo) {
        $sql = "SELECT p.*, u.nombre, u.apellido
                FROM periodos_tutoria p
                LEFT JOIN usuarios u ON p.creado_por = u.id_usuario
                WHERE p.id_periodo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_periodo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($codigo, $nombre, $fecha_inicio, $fecha_fin, $activo, $creado_por = null) {
        $sql = "INSERT INTO periodos_tutoria (codigo, nombre, fecha_inicio, fecha_fin, activo, creado_por)
                VALUES (:codigo, :nombre, :fecha_inicio, :fecha_fin, :activo, :creado_por)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
        $stmt->bindParam(':creado_por', $creado_por, $creado_por ? PDO::PARAM_INT : PDO::PARAM_NULL);
        return $stmt->execute();
    }

    public function actualizar($id_periodo, $codigo, $nombre, $fecha_inicio, $fecha_fin, $activo) {
        $sql = "UPDATE periodos_tutoria
                SET codigo = :codigo, nombre = :nombre, fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin, activo = :activo
                WHERE id_periodo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':fecha_fin', $fecha_fin);
        $stmt->bindParam(':activo', $activo, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id_periodo, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function eliminar($id_periodo) {
        $sql = "DELETE FROM periodos_tutoria WHERE id_periodo = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_periodo, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>