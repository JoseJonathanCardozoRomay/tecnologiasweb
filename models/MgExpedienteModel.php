<?php
/**
 * Modelo para la gestión de Expedientes de Modalidades de Grado
 */
require_once __DIR__ . '/../config/conexion.php';
class MgExpedienteModel {
    private $conexion;
    private $tabla = 'expedientes';

    public function __construct() {
        global $conexion;
        $this->conexion = $conexion;
    }

    public function listarTodos($filtros = []) {
        $sql = "SELECT e.*,
                       u_e.nombre AS estudiante_nombre, u_e.apellido AS estudiante_apellido,
                       m.nombre AS modalidad_nombre,
                       c.codigo AS cohorte_codigo
                FROM {$this->tabla} e
                LEFT JOIN estudiantes est ON e.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios u_e ON est.id_usuario = u_e.id_usuario
                LEFT JOIN mg_modalidades m ON e.id_modalidad = m.id_modalidad
                LEFT JOIN mg_cohortes c ON e.id_cohorte = c.id_cohorte
                WHERE 1=1";
        
        $parametros = [];
        if (!empty($filtros['id_cohorte'])) {
            $sql .= " AND e.id_cohorte = :id_cohorte";
            $parametros[':id_cohorte'] = $filtros['id_cohorte'];
        }
        if (!empty($filtros['id_modalidad'])) {
            $sql .= " AND e.id_modalidad = :id_modalidad";
            $parametros[':id_modalidad'] = $filtros['id_modalidad'];
        }
        if (!empty($filtros['etapa_actual'])) {
            $sql .= " AND e.etapa_actual = :etapa";
            $parametros[':etapa'] = $filtros['etapa_actual'];
        }
        if (!empty($filtros['buscar'])) {
            $sql .= " AND (u_e.nombre LIKE :buscar OR u_e.apellido LIKE :buscar)";
            $parametros[':buscar'] = "%{$filtros['buscar']}%";
        }
        $sql .= " ORDER BY e.id_expediente DESC";
        
        $stmt = $this->conexion->prepare($sql);
        foreach ($parametros as $clave => $valor) {
            $stmt->bindValue($clave, $valor); // ✅ Usamos bindValue
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id_expediente) {
        $sql = "SELECT e.*,
                       u_e.nombre AS estudiante_nombre, u_e.apellido AS estudiante_apellido,
                       m.nombre AS modalidad_nombre,
                       c.codigo AS cohorte_codigo
                FROM {$this->tabla} e
                LEFT JOIN estudiantes est ON e.id_estudiante = est.id_estudiante
                LEFT JOIN usuarios u_e ON est.id_usuario = u_e.id_usuario
                LEFT JOIN mg_modalidades m ON e.id_modalidad = m.id_modalidad
                LEFT JOIN mg_cohortes c ON e.id_cohorte = c.id_cohorte
                WHERE e.id_expediente = :id_expediente";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_expediente', $id_expediente); // ✅ bindValue
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($datos) {
        $sql = "INSERT INTO {$this->tabla} 
                (id_estudiante, id_modalidad, id_cohorte, fecha_inicio, titulo_trabajo, etapa_actual, estado, observaciones)
                VALUES 
                (:id_estudiante, :id_modalidad, :id_cohorte, :fecha_inicio, :titulo_trabajo, :etapa_actual, :estado, :observaciones)";
        
        $stmt = $this->conexion->prepare($sql);
        
        // ✅ Asignamos a variables PRIMERO
        $id_estudiante = $datos['id_estudiante'];
        $id_modalidad = $datos['id_modalidad'];
        $id_cohorte = $datos['id_cohorte'];
        $fecha_inicio = $datos['fecha_inicio'];
        $titulo_trabajo = $datos['titulo_trabajo'] ?? '';
        $etapa_actual = $datos['etapa_actual'] ?? 'previa';
        $estado = $datos['estado'] ?? 'activo';
        $observaciones = $datos['observaciones'] ?? '';
        
        // ✅ Ahora pasamos las variables
        $stmt->bindParam(':id_estudiante', $id_estudiante);
        $stmt->bindParam(':id_modalidad', $id_modalidad);
        $stmt->bindParam(':id_cohorte', $id_cohorte);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':titulo_trabajo', $titulo_trabajo);
        $stmt->bindParam(':etapa_actual', $etapa_actual);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        
        return $stmt->execute();
    }

    public function actualizar($id_expediente, $datos) {
        $sql = "UPDATE {$this->tabla} 
                SET id_estudiante = :id_estudiante,
                    id_modalidad = :id_modalidad,
                    id_cohorte = :id_cohorte,
                    fecha_inicio = :fecha_inicio,
                    titulo_trabajo = :titulo_trabajo,
                    etapa_actual = :etapa_actual,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id_expediente = :id_expediente";
        
        $stmt = $this->conexion->prepare($sql);
        
        // ✅ Variables primero
        $id_exp = $id_expediente;
        $id_estudiante = $datos['id_estudiante'];
        $id_modalidad = $datos['id_modalidad'];
        $id_cohorte = $datos['id_cohorte'];
        $fecha_inicio = $datos['fecha_inicio'];
        $titulo_trabajo = $datos['titulo_trabajo'];
        $etapa_actual = $datos['etapa_actual'];
        $estado = $datos['estado'];
        $observaciones = $datos['observaciones'];
        
        $stmt->bindParam(':id_expediente', $id_exp);
        $stmt->bindParam(':id_estudiante', $id_estudiante);
        $stmt->bindParam(':id_modalidad', $id_modalidad);
        $stmt->bindParam(':id_cohorte', $id_cohorte);
        $stmt->bindParam(':fecha_inicio', $fecha_inicio);
        $stmt->bindParam(':titulo_trabajo', $titulo_trabajo);
        $stmt->bindParam(':etapa_actual', $etapa_actual);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        
        return $stmt->execute();
    }

    public function eliminar($id_expediente) {
        $sql = "DELETE FROM {$this->tabla} WHERE id_expediente = :id_expediente";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_expediente', $id_expediente); // ✅ bindValue
        return $stmt->execute();
    }
}