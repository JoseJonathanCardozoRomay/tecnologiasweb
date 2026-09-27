<?php
class MgReporteModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Reporte consolidado de expedientes de una cohorte. */
    public function consolidado($idCohorte)
    {
        $sql = "SELECT e.id_expediente_mg, e.estado AS estado_expediente, e.fecha_solicitud,
                       e.resolucion_admin, e.fecha_validacion,
                       est.registro_universitario,
                       u.nombre AS estudiante_nombre, u.apellido AS estudiante_apellido,
                       ca.nombre_carrera,
                       mg.nombre AS modalidad_nombre,
                       ac.id_tutor AS id_tutor_activo,
                       ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido,
                       d.fecha_defensa, d.hora_inicio, d.hora_fin, d.nota_final,
                       d.resultado, d.estado AS estado_defensa
                FROM expedientes_mg e
                JOIN estudiantes est ON est.id_estudiante = e.id_estudiante
                JOIN usuarios u ON u.id_usuario = est.id_usuario
                JOIN carreras ca ON ca.id_carrera = est.id_carrera
                JOIN modalidades_grado mg ON mg.id_modalidad_grado = e.id_modalidad_grado
                LEFT JOIN asignaciones_tutor ac ON ac.id_expediente_mg = e.id_expediente_mg
                                               AND ac.estado = 'activa'
                LEFT JOIN tutores tt ON tt.id_tutor = ac.id_tutor
                LEFT JOIN usuarios ut ON ut.id_usuario = tt.id_usuario
                LEFT JOIN defensas_mg d ON d.id_expediente_mg = e.id_expediente_mg
                WHERE e.id_cohorte_mg = :cohorte
                ORDER BY u.apellido, u.nombre, e.id_expediente_mg ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cohorte' => (int) $idCohorte]);

        return $stmt->fetchAll();
    }

    public function contarPorCohorte($idCohorte): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM expedientes_mg WHERE id_cohorte_mg = :coh");
        $stmt->execute([':coh' => (int) $idCohorte]);

        return (int) $stmt->fetchColumn();
    }
}