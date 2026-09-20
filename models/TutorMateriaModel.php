<?php
declare(strict_types=1);

/**
 * Gestiona la relación entre tutores y materias.
 */
class TutorMateriaModel
{
    public function __construct(private PDO $pdo) {}

    public function obtenerTodos(): array
    {
        return $this->pdo->query(
            "SELECT tm.id_tutor, tm.id_materia, u.nombre, u.apellido,
                    m.nombre_materia, c.nombre_carrera, m.estado AS estado_materia
             FROM tutor_materia tm
             INNER JOIN tutores t ON t.id_tutor=tm.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             INNER JOIN materias m ON m.id_materia=tm.id_materia
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             ORDER BY u.apellido,u.nombre,m.nombre_materia"
        )->fetchAll();
    }

    public function porTutor(int $id): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT tm.*,m.nombre_materia,c.nombre_carrera,m.estado AS estado_materia
             FROM tutor_materia tm
             INNER JOIN materias m ON m.id_materia=tm.id_materia
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             WHERE tm.id_tutor=:id ORDER BY m.nombre_materia'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll();
    }

    public function materiasNoAsignadas(int $id): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT m.*,c.nombre_carrera
             FROM materias m
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             WHERE m.estado='activo' AND c.estado='activo'
               AND NOT EXISTS(SELECT 1 FROM tutor_materia tm WHERE tm.id_tutor=:t AND tm.id_materia=m.id_materia)
             ORDER BY m.nombre_materia"
        );
        $stmt->execute([':t' => $id]);
        return $stmt->fetchAll();
    }

    public function tutorActivo(int $idTutor): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tutores t
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE t.id_tutor=:t AND u.estado='activo'"
        );
        $stmt->execute([':t' => $idTutor]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function materiaActiva(int $idMateria): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM materias m
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             WHERE m.id_materia=:m AND m.estado='activo' AND c.estado='activo'"
        );
        $stmt->execute([':m' => $idMateria]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function asignar(int $t, int $m): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO tutor_materia(id_tutor,id_materia) VALUES(:t,:m)'
        )->execute([':t' => $t, ':m' => $m]);
    }

    public function quitar(int $t, int $m): bool
    {
        return $this->pdo->prepare(
            'DELETE FROM tutor_materia WHERE id_tutor=:t AND id_materia=:m'
        )->execute([':t' => $t, ':m' => $m]);
    }
}
