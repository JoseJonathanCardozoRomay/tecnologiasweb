<?php
class EstudianteModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerPorUsuario($id_usuario)
    {
        $sql = "SELECT e.*, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_usuario = :id_usuario
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);

        return $stmt->fetch();
    }

    public function obtenerPorId($id_estudiante)
    {
        $sql = "SELECT e.*, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE e.id_estudiante = :id_estudiante
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return $stmt->fetch();
    }

    public function obtenerTodosConCarrera()
    {
        $sql = "SELECT e.id_estudiante, e.id_usuario, e.id_carrera, e.registro_universitario,
                       u.nombre, u.apellido, c.nombre_carrera
                FROM estudiantes e
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE u.estado = 'activo'
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function puedeAccederMG($id_estudiante, ConfiguracionModel $configuracion)
    {
        $estudiante = $this->obtenerPorId($id_estudiante);
        if (!$estudiante) {
            return false;
        }

        $materiasRequeridas = $configuracion->int('materias_requeridas_mg', 54);
        $semestresRequeridos = $configuracion->int('semestres_requeridos_mg', 9);

        $cumpleMaterias = (int) $estudiante['materias_completadas'] >= $materiasRequeridas;
        $cumpleSemestres = (int) $estudiante['semestre'] >= $semestresRequeridos;

        return (int) $estudiante['acceso_mg_desbloqueado'] === 1
            && $cumpleMaterias
            && $cumpleSemestres;
    }

    public function desbloquearMG($id_estudiante, $id_operador)
    {
        return $this->cambiarAccesoMG($id_estudiante, 1, $id_operador);
    }

    public function bloquearMG($id_estudiante)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE estudiantes
             SET acceso_mg_desbloqueado = 0, mg_desbloqueado_por = NULL, mg_desbloqueado_fecha = NULL
             WHERE id_estudiante = :id_estudiante"
        );
        return $stmt->execute([':id_estudiante' => $id_estudiante]);
    }

    private function cambiarAccesoMG($id_estudiante, $estado, $id_operador)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE estudiantes
             SET acceso_mg_desbloqueado = :estado,
                 mg_desbloqueado_por = :operador,
                 mg_desbloqueado_fecha = NOW()
             WHERE id_estudiante = :id_estudiante"
        );
        return $stmt->execute([
            ':estado'     => $estado,
            ':operador'   => $id_operador,
            ':id_estudiante' => $id_estudiante,
        ]);
    }

    public function actualizarMateriasCompletadas($id_estudiante, $cantidad)
    {
        $cantidad = max(0, (int) $cantidad);
        $stmt = $this->pdo->prepare(
            "UPDATE estudiantes SET materias_completadas = :cantidad WHERE id_estudiante = :id_estudiante"
        );
        return $stmt->execute([
            ':cantidad'     => $cantidad,
            ':id_estudiante' => $id_estudiante,
        ]);
    }
}