<?php
declare(strict_types=1);

/**
 * Modelo de los horarios grupales definidos por la universidad.
 *
 * IMPORTANTE: estos horarios NO utilizan disponibilidad_tutor.
 * Los tres bloques oficiales son inmutables para estudiantes y tutores:
 * 07:00-10:00, 15:00-18:00 y 19:00-22:00, de lunes a viernes.
 */
class HorarioGrupalModel
{
    private const TURNOS = [
        'manana' => ['inicio' => '07:00:00', 'fin' => '10:00:00'],
        'tarde' => ['inicio' => '15:00:00', 'fin' => '18:00:00'],
        'noche' => ['inicio' => '19:00:00', 'fin' => '22:00:00'],
    ];

    private const DIAS = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes'];

    public function __construct(private PDO $pdo) {}

    /** Lista horarios con materia, carrera y tutor. */
    public function obtenerTodos(?string $estado = null): array
    {
        $sql = "SELECT h.*, m.nombre_materia, c.nombre_carrera,
                       t.id_tutor, u.nombre AS tutor_nombre, u.apellido AS tutor_apellido
                FROM horarios_tutoria_grupal h
                INNER JOIN materias m ON m.id_materia=h.id_materia
                INNER JOIN carreras c ON c.id_carrera=m.id_carrera
                INNER JOIN tutores t ON t.id_tutor=h.id_tutor
                INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
                WHERE 1=1";
        $params = [];

        if ($estado !== null && in_array($estado, ['activo', 'inactivo'], true)) {
            $sql .= ' AND h.estado=:estado';
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY h.id_horario ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Obtiene un horario por ID. */
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT h.*, m.nombre_materia, c.nombre_carrera,
                    t.id_tutor, u.nombre AS tutor_nombre, u.apellido AS tutor_apellido
             FROM horarios_tutoria_grupal h
             INNER JOIN materias m ON m.id_materia=h.id_materia
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             INNER JOIN tutores t ON t.id_tutor=h.id_tutor
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE h.id_horario=:id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Devuelve los bloques oficiales para construir formularios sin valores inventados. */
    public static function bloquesOficiales(): array
    {
        return self::TURNOS;
    }

    /** Devuelve los días permitidos por la universidad para tutoría grupal. */
    public static function diasPermitidos(): array
    {
        return self::DIAS;
    }

    /** Valida día, turno y horas contra los bloques oficiales. */
    public function validarBloque(array $d): array
    {
        $errores = [];
        $dia = (string)($d['dia_semana'] ?? '');
        $turno = (string)($d['turno'] ?? '');
        $inicio = (string)($d['hora_inicio'] ?? '');
        $fin = (string)($d['hora_fin'] ?? '');

        if (!in_array($dia, self::DIAS, true)) {
            $errores[] = 'El horario grupal debe ser de lunes a viernes.';
        }

        if (!isset(self::TURNOS[$turno])) {
            $errores[] = 'El turno seleccionado no es válido.';
            return $errores;
        }

        $bloque = self::TURNOS[$turno];
        if ($inicio !== $bloque['inicio'] || $fin !== $bloque['fin']) {
            $errores[] = 'El horario no coincide con el bloque oficial de la universidad.';
        }

        return $errores;
    }

    /** Comprueba que el tutor esté activo. */
    public function tutorActivo(int $idTutor): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM tutores t
             INNER JOIN usuarios u ON u.id_usuario=t.id_usuario
             WHERE t.id_tutor=:id AND u.estado='activo'"
        );
        $stmt->execute([':id' => $idTutor]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Comprueba que la materia esté activa y su carrera también. */
    public function materiaActiva(int $idMateria): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM materias m
             INNER JOIN carreras c ON c.id_carrera=m.id_carrera
             WHERE m.id_materia=:id AND m.estado='activo' AND c.estado='activo'"
        );
        $stmt->execute([':id' => $idMateria]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Comprueba que el tutor tenga asignada la materia. */
    public function tutorTieneMateria(int $idTutor, int $idMateria): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM tutor_materia WHERE id_tutor=:t AND id_materia=:m'
        );
        $stmt->execute([':t' => $idTutor, ':m' => $idMateria]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Detecta si el tutor ya ocupa el mismo bloque. */
    public function existeConflictoTutor(array $d, ?int $excepto = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM horarios_tutoria_grupal
                WHERE id_tutor=:t AND dia_semana=:dia
                  AND hora_inicio=:hi AND hora_fin=:hf';
        $params = [
            ':t' => $d['id_tutor'], ':dia' => $d['dia_semana'],
            ':hi' => $d['hora_inicio'], ':hf' => $d['hora_fin'],
        ];
        if ($excepto !== null) {
            $sql .= ' AND id_horario<>:id';
            $params[':id'] = $excepto;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Valida las reglas de negocio antes de crear o actualizar un horario. */
    public function validar(array $d, ?int $excepto = null): array
    {
        $errores = $this->validarBloque($d);
        $idTutor = (int)($d['id_tutor'] ?? 0);
        $idMateria = (int)($d['id_materia'] ?? 0);

        if ($idTutor <= 0 || !$this->tutorActivo($idTutor)) {
            $errores[] = 'El tutor seleccionado no existe o está inactivo.';
        }
        if ($idMateria <= 0 || !$this->materiaActiva($idMateria)) {
            $errores[] = 'La materia seleccionada no existe o está inactiva.';
        }
        if ($idTutor > 0 && $idMateria > 0 && $this->tutorActivo($idTutor) && $this->materiaActiva($idMateria)
            && !$this->tutorTieneMateria($idTutor, $idMateria)) {
            $errores[] = 'El tutor no está asignado a la materia seleccionada.';
        }
        if ($idTutor > 0 && in_array((string)($d['dia_semana'] ?? ''), self::DIAS, true)
            && isset(self::TURNOS[$d['turno'] ?? '']) && $this->existeConflictoTutor($d, $excepto)) {
            $errores[] = 'El tutor ya tiene otro horario grupal en ese bloque.';
        }

        return array_values(array_unique($errores));
    }

    /** Crea un horario oficial. Solo debe ser llamado desde un controlador autorizado. */
    public function crear(array $d): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO horarios_tutoria_grupal(id_materia,id_tutor,dia_semana,turno,hora_inicio,hora_fin,estado)
             VALUES(:m,:t,:d,:turno,:hi,:hf,:estado)'
        )->execute([
            ':m' => $d['id_materia'], ':t' => $d['id_tutor'], ':d' => $d['dia_semana'],
            ':turno' => $d['turno'], ':hi' => $d['hora_inicio'], ':hf' => $d['hora_fin'],
            ':estado' => $d['estado'] ?? 'activo',
        ]);
    }

    /** Actualiza un horario existente. */
    public function actualizar(int $id, array $d): bool
    {
        return $this->pdo->prepare(
            'UPDATE horarios_tutoria_grupal
             SET id_materia=:m,id_tutor=:t,dia_semana=:d,turno=:turno,
                 hora_inicio=:hi,hora_fin=:hf,estado=:estado
             WHERE id_horario=:id'
        )->execute([
            ':m' => $d['id_materia'], ':t' => $d['id_tutor'], ':d' => $d['dia_semana'],
            ':turno' => $d['turno'], ':hi' => $d['hora_inicio'], ':hf' => $d['hora_fin'],
            ':estado' => $d['estado'], ':id' => $id,
        ]);
    }

    /** Cambia el estado sin borrar el historial del horario. */
    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            return false;
        }
        return $this->pdo->prepare(
            'UPDATE horarios_tutoria_grupal SET estado=:estado WHERE id_horario=:id'
        )->execute([':estado' => $estado, ':id' => $id]);
    }
}
