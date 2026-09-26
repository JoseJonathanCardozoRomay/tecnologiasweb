<?php
require_once __DIR__ . '/../config/conexion.php';
class MetricaSeguimientoModel {
    private $conexion;
    private $tabla = 'metricas_seguimiento';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function obtenerDashboard() {
        $metricas = [];
        
        // Total tutorías activas
        $stmt = $this->conexion->query("SELECT COUNT(*) FROM tutorias WHERE estado != 'cancelada'");
        $metricas['total_tutorias'] = $stmt->fetchColumn();
        
        // Con reuniones registradas
        $stmt = $this->conexion->query("SELECT COUNT(DISTINCT id_tutoria) FROM reuniones_seguimiento");
        $metricas['con_reuniones'] = $stmt->fetchColumn();
        
        // Informes por estado
        $stmt = $this->conexion->query("SELECT estado, COUNT(*) as total FROM informes_avance GROUP BY estado");
        $metricas['informes_por_estado'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Promedio de avance
        $stmt = $this->conexion->query("SELECT AVG(progreso_porcentaje) FROM informes_avance WHERE estado != 'borrador'");
        $metricas['promedio_avance'] = round($stmt->fetchColumn() ?: 0, 1);
        
        // Alertas urgentes
        $stmt = $this->conexion->query("SELECT COUNT(*) FROM alertas_seguimiento WHERE nivel = 'urgente' AND leida = 0");
        $metricas['alertas_urgentes'] = $stmt->fetchColumn();
        
        return $metricas;
    }
}