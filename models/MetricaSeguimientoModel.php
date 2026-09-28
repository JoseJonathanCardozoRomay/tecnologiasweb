 <?php
/**
 * Modelo Métricas de Seguimiento
 * Usa nombres de columnas reales de la tabla: seguimiento_sesion
 */
require_once __DIR__ . '/../config/conexion.php';

class MetricaSeguimientoModel {
    private $conexion;

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function obtenerDashboard() {
        $datos = [];
        
        // ✅ Columna correcta: avance
        $stmt = $this->conexion->query("SELECT COUNT(*) FROM seguimiento_sesion WHERE avance = 'logrado'");
        $datos['logrados'] = $stmt->fetchColumn();

        $stmt = $this->conexion->query("SELECT COUNT(*) FROM seguimiento_sesion WHERE avance = 'parcial'");
        $datos['parciales'] = $stmt->fetchColumn();

        $stmt = $this->conexion->query("SELECT COUNT(*) FROM seguimiento_sesion WHERE avance = 'sin_avance'");
        $datos['sin_avance'] = $stmt->fetchColumn();

        // ✅ Columna correcta: asistio
        $stmt = $this->conexion->query("SELECT COUNT(*) FROM seguimiento_sesion WHERE asistio = 'si'");
        $datos['asistieron'] = $stmt->fetchColumn();

        $stmt = $this->conexion->query("SELECT COUNT(*) FROM seguimiento_sesion WHERE asistio = 'no'");
        $datos['no_asistieron'] = $stmt->fetchColumn();

        return $datos;
    }
}