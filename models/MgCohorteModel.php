 <?php
/**
 * Modelo Cohortes de Grado — Completo y sin errores
 * Coincide EXACTAMENTE con tu tabla: mg_cohortes
 */
require_once __DIR__ . '/../config/conexion.php';

class MgCohorteModel {
    private $conexion;
    private $tabla = 'mg_cohortes';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    // ✅ Ahora funciona con AMBOS nombres: listarTodos y listarTodas
    public function listarTodos() {
        return $this->listarTodas();
    }

    public function listarTodas() {
        $sql = "SELECT * FROM {$this->tabla} ORDER BY codigo DESC, nombre ASC";
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM {$this->tabla} WHERE id_cohorte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} (codigo, nombre, fecha_inicio, fecha_fin, activo)
                VALUES (:codigo, :nombre, :fecha_inicio, :fecha_fin, :activo)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':codigo'       => $datos['codigo'],
            ':nombre'       => $datos['nombre'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin'    => !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null,
            ':activo'       => $datos['activo'] ?? 1
        ]);
        return $this->conexion->lastInsertId();
    }

    // ✅ Agregados para que Editar y Eliminar también funcionen
    public function editar($id, $datos) {
        $sql = "UPDATE {$this->tabla} 
                SET codigo = :codigo, nombre = :nombre, fecha_inicio = :fecha_inicio, 
                    fecha_fin = :fecha_fin, activo = :activo
                WHERE id_cohorte = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id'           => (int)$id,
            ':codigo'       => $datos['codigo'],
            ':nombre'       => $datos['nombre'],
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin'    => !empty($datos['fecha_fin']) ? $datos['fecha_fin'] : null,
            ':activo'       => $datos['activo'] ?? 0
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_cohorte = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
    }
}