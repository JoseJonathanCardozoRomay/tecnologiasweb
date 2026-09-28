 <?php
/**
 * Modelo Expediente de Modalidad de Grado
 * Agregado: campo estudiante_nombre para mostrar correctamente
 */
require_once __DIR__ . '/../config/conexion.php';
class MgExpedienteModel {
    private $conexion;
    private $tabla = 'expedientes_mg';
    
    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }
    
    public function listarTodos() {
        $sql = "SELECT e.*,
                       u.nombre, u.apellido, 
                       CONCAT(u.nombre, ' ', u.apellido) AS estudiante_nombre,
                       est.codigo_estudiante,
                       m.nombre AS modalidad_nombre,
                       c.codigo AS cohorte_codigo, c.nombre AS cohorte_nombre
                FROM {$this->tabla} e
                INNER JOIN estudiantes est ON e.id_estudiante = est.id_estudiante
                INNER JOIN usuarios u ON est.id_usuario = u.id_usuario
                LEFT JOIN mg_modalidades m ON e.id_modalidad = m.id_modalidad
                LEFT JOIN mg_cohortes c ON e.id_cohorte = c.id_cohorte
                ORDER BY e.fecha_inicio DESC";
        
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id_expediente) {
        $id_expediente = (int)$id_expediente;
        $sql = "SELECT e.*,
                       u.nombre, u.apellido,
                       CONCAT(u.nombre, ' ', u.apellido) AS estudiante_nombre,
                       est.codigo_estudiante,
                       m.nombre AS modalidad_nombre,
                       c.codigo AS cohorte_codigo, c.nombre AS cohorte_nombre
                FROM {$this->tabla} e
                INNER JOIN estudiantes est ON e.id_estudiante = est.id_estudiante
                INNER JOIN usuarios u ON est.id_usuario = u.id_usuario
                LEFT JOIN mg_modalidades m ON e.id_modalidad = m.id_modalidad
                LEFT JOIN mg_cohortes c ON e.id_cohorte = c.id_cohorte
                WHERE e.id_expediente = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id_expediente, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla}
                (id_estudiante, id_modalidad, id_cohorte, fecha_inicio, titulo_trabajo, observaciones)
                VALUES (:id_estudiante, :id_modalidad, :id_cohorte, :fecha_inicio, :titulo, :obs)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_estudiante' => (int)($datos['id_estudiante'] ?? 0),
            ':id_modalidad'  => (int)($datos['id_modalidad'] ?? 0),
            ':id_cohorte'    => (int)($datos['id_cohorte'] ?? 0),
            ':fecha_inicio'  => $datos['fecha_inicio'] ?? '',
            ':titulo'        => !empty($datos['titulo_trabajo']) ? $datos['titulo_trabajo'] : null,
            ':obs'           => !empty($datos['observaciones']) ? $datos['observaciones'] : null
        ]);
        return $this->conexion->lastInsertId();
    }
    
    public function editar($id_expediente, $datos) {
        $id_expediente = (int)$id_expediente;
        $sql = "UPDATE {$this->tabla}
                SET id_estudiante = :id_estudiante,
                    id_modalidad  = :id_modalidad,
                    id_cohorte    = :id_cohorte,
                    fecha_inicio  = :fecha_inicio,
                    titulo_trabajo = :titulo,
                    observaciones = :obs
                WHERE id_expediente = :id";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            ':id_estudiante' => (int)($datos['id_estudiante'] ?? 0),
            ':id_modalidad'  => (int)($datos['id_modalidad'] ?? 0),
            ':id_cohorte'    => (int)($datos['id_cohorte'] ?? 0),
            ':fecha_inicio'  => $datos['fecha_inicio'] ?? '',
            ':titulo'        => !empty($datos['titulo_trabajo']) ? $datos['titulo_trabajo'] : null,
            ':obs'           => !empty($datos['observaciones']) ? $datos['observaciones'] : null,
            ':id'            => $id_expediente
        ]);
        return true;
    }
    
    public function eliminar($id_expediente) {
        $id_expediente = (int)$id_expediente;
        $stmt = $this->conexion->prepare("DELETE FROM {$this->tabla} WHERE id_expediente = :id");
        $stmt->bindParam(':id', $id_expediente, PDO::PARAM_INT);
        return $stmt->execute();
    }
}