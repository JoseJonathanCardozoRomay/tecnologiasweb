<?php
declare(strict_types=1);

/**
 * Gestiona los proyectos de grado asociados a estudiantes.
 *
 * Un proyecto de grado pertenece a un estudiante y a su carrera.
 * Este modelo no modifica la configuración de conexión y utiliza PDO
 * mediante la misma instancia que emplea el resto de la aplicación.
 */
class ProyectoGradoModel
{
    public function __construct(private PDO $pdo) {}

    /** Lista proyectos con información del estudiante y carrera. */
    public function obtenerTodos(?string $busqueda = null, ?string $estado = null): array
    {
        $sql = "SELECT p.*, e.id_estudiante, e.registro_universitario,
                       u.nombre, u.apellido, u.correo,
                       c.nombre_carrera
                FROM proyectos_grado p
                INNER JOIN estudiantes e ON e.id_estudiante = p.id_estudiante
                INNER JOIN usuarios u ON u.id_usuario = e.id_usuario
                INNER JOIN carreras c ON c.id_carrera = p.id_carrera
                WHERE 1=1";
        $params = [];

        if ($busqueda !== null && trim($busqueda) !== '') {
            $sql .= " AND (LOWER(p.titulo) LIKE LOWER(:q)
                       OR LOWER(CONCAT(u.nombre,' ',u.apellido)) LIKE LOWER(:q2)
                       OR LOWER(c.nombre_carrera) LIKE LOWER(:q3))";
            $like = '%' . trim($busqueda) . '%';
            $params[':q'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
        }

        if ($estado !== null && in_array($estado, ['propuesto', 'en_proceso', 'finalizado', 'cancelado'], true)) {
            $sql .= ' AND p.estado=:estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' ORDER BY p.id_proyecto DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Obtiene un proyecto por identificador. */
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, e.registro_universitario,
                    u.nombre, u.apellido, u.correo,
                    c.nombre_carrera
             FROM proyectos_grado p
             INNER JOIN estudiantes e ON e.id_estudiante=p.id_estudiante
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN carreras c ON c.id_carrera=p.id_carrera
             WHERE p.id_proyecto=:id
             LIMIT 1"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /** Obtiene los proyectos de un estudiante. */
    public function porEstudiante(int $idEstudiante): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, c.nombre_carrera
             FROM proyectos_grado p
             INNER JOIN carreras c ON c.id_carrera=p.id_carrera
             WHERE p.id_estudiante=:id
             ORDER BY p.id_proyecto DESC'
        );
        $stmt->execute([':id' => $idEstudiante]);
        return $stmt->fetchAll();
    }

    /** Comprueba que estudiante y carrera coincidan. */
    public function estudiantePerteneceACarrera(int $idEstudiante, int $idCarrera): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*)
             FROM estudiantes e
             INNER JOIN usuarios u ON u.id_usuario=e.id_usuario
             INNER JOIN carreras c ON c.id_carrera=e.id_carrera
             WHERE e.id_estudiante=:e
               AND e.id_carrera=:c
               AND u.estado="activo"
               AND c.estado="activo"'
        );
        $stmt->execute([':e' => $idEstudiante, ':c' => $idCarrera]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Comprueba que el título no esté repetido dentro de la misma carrera. */
    public function existeTitulo(string $titulo, int $idCarrera, ?int $excepto = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM proyectos_grado WHERE id_carrera=:c AND LOWER(TRIM(titulo))=LOWER(TRIM(:t))';
        $params = [':c' => $idCarrera, ':t' => $titulo];
        if ($excepto !== null) {
            $sql .= ' AND id_proyecto<>:id';
            $params[':id'] = $excepto;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Determina si el proyecto tiene una tutoría personal actualmente
     * programada o en ejecución. Mientras exista una de estas sesiones,
     * el estudiante no puede modificar el proyecto ni enviar nuevas
     * solicitudes de tutoría para evitar cambios sobre una agenda ya confirmada.
     */
    public function tieneTutoriaActiva(int $idProyecto): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM tutorias
             WHERE id_proyecto=:proyecto
               AND estado IN ('programada','en_proceso')"
        );
        $stmt->execute([':proyecto' => $idProyecto]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Crea un proyecto de grado. */
    public function crear(array $d): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO proyectos_grado(id_estudiante,id_carrera,titulo,descripcion,estado)
             VALUES(:e,:c,:t,:d,:estado)'
        )->execute([
            ':e' => $d['id_estudiante'],
            ':c' => $d['id_carrera'],
            ':t' => trim((string)$d['titulo']),
            ':d' => trim((string)($d['descripcion'] ?? '')) ?: null,
            ':estado' => $d['estado'] ?? 'propuesto',
        ]);
    }

    /** Actualiza un proyecto sin modificar su propietario accidentalmente. */
    public function actualizar(int $id, array $d): bool
    {
        return $this->pdo->prepare(
            'UPDATE proyectos_grado
             SET id_estudiante=:e,id_carrera=:c,titulo=:t,descripcion=:d,estado=:estado
             WHERE id_proyecto=:id'
        )->execute([
            ':e' => $d['id_estudiante'],
            ':c' => $d['id_carrera'],
            ':t' => trim((string)$d['titulo']),
            ':d' => trim((string)($d['descripcion'] ?? '')) ?: null,
            ':estado' => $d['estado'],
            ':id' => $id,
        ]);
    }

    /**
     * Registra el resultado de la defensa del proyecto.
     *
     * Aprobado: cambia el proyecto a `finalizado` (estado interno que la
     * interfaz presenta como "Concluido".
     * Reprobado: conserva `en_proceso` para que el estudiante pueda continuar
     * trabajando y solicitando nuevas tutorías cuando no exista una sesión
     * activa.
     *
     * Para cerrar un proyecto se verifica además que no existan tutorías
     * personales pendientes, programadas o en ejecución.
     */
    public function registrarResultadoDefensa(int $idProyecto, string $resultado): bool
    {
        if (!in_array($resultado, ['aprobado', 'reprobado'], true)) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'SELECT estado FROM proyectos_grado WHERE id_proyecto=:id FOR UPDATE'
            );
            $stmt->execute([':id' => $idProyecto]);
            $proyecto = $stmt->fetch();

            if (!$proyecto || (string)$proyecto['estado'] !== 'en_proceso') {
                $this->pdo->rollBack();
                return false;
            }

            if ($resultado === 'reprobado') {
                $this->pdo->commit();
                return true;
            }

            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM tutorias
                 WHERE id_proyecto=:id
                   AND estado IN ('pendiente','programada','en_proceso')"
            );
            $stmt->execute([':id' => $idProyecto]);
            if ((int)$stmt->fetchColumn() > 0) {
                $this->pdo->rollBack();
                return false;
            }

            $stmt = $this->pdo->prepare(
                "UPDATE proyectos_grado
                 SET estado='finalizado'
                 WHERE id_proyecto=:id AND estado='en_proceso'"
            );
            $stmt->execute([':id' => $idProyecto]);

            if ($stmt->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /** Elimina un proyecto; las tutorías lo conservan como NULL por la FK. */
    public function eliminar(int $id): bool
    {
        return $this->pdo->prepare('DELETE FROM proyectos_grado WHERE id_proyecto=:id')->execute([':id' => $id]);
    }
}
