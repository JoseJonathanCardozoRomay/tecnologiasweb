<?php
require_once __DIR__ . '/ConfiguracionModel.php';

class CartaModel
{
    private const ESTADOS = ['pendiente', 'aceptada', 'rechazada'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function crear(int $id_tutoria, int $id_tutor, int $id_estudiante): int
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO cartas_designacion (id_tutoria, id_tutor, id_estudiante, estado)
                 VALUES (:id_tutoria, :id_tutor, :id_estudiante, 'pendiente')"
            );
            $stmt->execute([
                ':id_tutoria'   => $id_tutoria,
                ':id_tutor'     => $id_tutor,
                ':id_estudiante'=> $id_estudiante,
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Error al generar la carta de designación.');
        }
    }

    public function obtenerPorTutoria(int $id_tutoria)
    {
        $sql = "SELECT c.*,
                       tut_nombre.nombre AS tutor_nombre, tut_nombre.apellido AS tutor_apellido,
                       est_nombre.nombre AS estudiante_nombre, est_nombre.apellido AS estudiante_apellido,
                       m.nombre_materia, md.nombre AS modalidad_nombre, t.estado AS estado_tutoria,
                       t.id_modalidad, t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin
                FROM cartas_designacion c
                INNER JOIN tutorias t ON c.id_tutoria = t.id_tutoria
                INNER JOIN tutores tut ON c.id_tutor = tut.id_tutor
                INNER JOIN usuarios tut_nombre ON tut.id_usuario = tut_nombre.id_usuario
                INNER JOIN estudiantes est ON c.id_estudiante = est.id_estudiante
                INNER JOIN usuarios est_nombre ON est.id_usuario = est_nombre.id_usuario
                INNER JOIN materias m ON t.id_materia = m.id_materia
                LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                WHERE c.id_tutoria = :id_tutoria
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetch();
    }

    public function obtenerPorTutor(int $id_tutor)
    {
        $sql = "SELECT c.id_carta, c.id_tutoria, c.fecha_generacion, c.fecha_firma, c.estado,
                       c.motivo_rechazo, c.id_estudiante,
                       tut_nombre.nombre AS tutor_nombre, tut_nombre.apellido AS tutor_apellido,
                       est_nombre.nombre AS estudiante_nombre, est_nombre.apellido AS estudiante_apellido,
                       m.nombre_materia, md.nombre AS modalidad_nombre, t.estado AS estado_tutoria,
                       t.fecha AS fecha_tutoria, t.hora_inicio, t.hora_fin
                FROM cartas_designacion c
                INNER JOIN tutorias t ON c.id_tutoria = t.id_tutoria
                INNER JOIN tutores tut ON c.id_tutor = tut.id_tutor
                INNER JOIN usuarios tut_nombre ON tut.id_usuario = tut_nombre.id_usuario
                INNER JOIN estudiantes est ON c.id_estudiante = est.id_estudiante
                INNER JOIN usuarios est_nombre ON est.id_usuario = est_nombre.id_usuario
                INNER JOIN materias m ON t.id_materia = m.id_materia
                LEFT JOIN modalidades_graduacion md ON t.id_modalidad = md.id_modalidad
                WHERE c.id_tutor = :id_tutor
                ORDER BY c.fecha_generacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);

        return $stmt->fetchAll();
    }

    public function contarTutoriasMG(int $id_tutor): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM tutorias
                WHERE id_tutor = :id_tutor
                  AND tipo = 'grado'
                  AND estado IN ('aceptada', 'en_proceso')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);

        return (int) $stmt->fetchColumn();
    }

    public function cupoDisponible(int $id_tutor): int
    {
        return max(0, $this->cupoMaximo() - $this->contarTutoriasMG($id_tutor));
    }

    public function cupoMaximo(): int
    {
        return (int) (new ConfiguracionModel($this->pdo))->int('cupo_maximo_mg', 5);
    }

    public function cuposMG(int $id_tutor): array
    {
        $maximo = $this->cupoMaximo();
        $usados = $this->contarTutoriasMG($id_tutor);
        $activas = $this->contarActivas($id_tutor);

        return [
            'maximo'       => $maximo,
            'usados'       => $usados,
            'disponibles'  => max(0, $maximo - $usados),
            'activas'      => $activas,
            'sobrecupo'    => $usados >= $maximo,
        ];
    }

    private function contarActivas(int $id_tutor): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM tutorias
                WHERE id_tutor = :id_tutor
                  AND estado IN ('aceptada', 'en_proceso')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);

        return (int) $stmt->fetchColumn();
    }

    public function aceptar(int $id_carta): bool
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE cartas_designacion
                 SET estado = 'aceptada', fecha_firma = NOW(), motivo_rechazo = NULL
                 WHERE id_carta = :id_carta AND estado = 'pendiente'"
            );
            $stmt->execute([':id_carta' => $id_carta]);

            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                throw new RuntimeException('La carta ya fue respondida o no existe.');
            }

            $carta = $this->obtenerPorId($id_carta);
            $updateTutoria = $this->pdo->prepare(
                "UPDATE tutorias SET estado = 'aceptada' WHERE id_tutoria = :id_tutoria"
            );
            $updateTutoria->execute([':id_tutoria' => $carta['id_tutoria']]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al aceptar la carta de designación.');
        }
    }

    public function rechazar(int $id_carta, string $motivo): bool
    {
        $motivo = trim($motivo);
        if ($motivo === '') {
            throw new InvalidArgumentException('Debes indicar el motivo del rechazo.');
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare(
                "UPDATE cartas_designacion
                 SET estado = 'rechazada', motivo_rechazo = :motivo, fecha_firma = NOW()
                 WHERE id_carta = :id_carta AND estado = 'pendiente'"
            );
            $stmt->execute([':motivo' => $motivo, ':id_carta' => $id_carta]);

            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                throw new RuntimeException('La carta ya fue respondida o no existe.');
            }

            $carta = $this->obtenerPorId($id_carta);
            $updateTutoria = $this->pdo->prepare(
                "UPDATE tutorias SET estado = 'en_reasignacion' WHERE id_tutoria = :id_tutoria"
            );
            $updateTutoria->execute([':id_tutoria' => $carta['id_tutoria']]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al rechazar la carta de designación.');
        }
    }

    public function obtenerPorId(int $id_carta)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cartas_designacion WHERE id_carta = :id");
        $stmt->execute([':id' => $id_carta]);
        return $stmt->fetch();
    }
}