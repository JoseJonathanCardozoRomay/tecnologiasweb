<?php

class CupoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Lista tutores, cupos y estudiantes inscritos durante el periodo. */
    public function listarPorPeriodo(
        int $idPeriodo,
        string $busqueda = ''
    ): array {
        $sql = "
            SELECT
                t.id_tutor,
                t.id_usuario,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,
                u.estado AS estado_usuario,
                t.especialidad,
                tp.id_tutor_periodo,
                tp.cupo_maximo,
                tp.activo,
                (
                    SELECT COUNT(DISTINCT tu.id_estudiante)
                    FROM tutorias AS tu
                    WHERE tu.id_tutor = t.id_tutor
                        AND tu.fecha_solicitud >= p.fecha_inicio
                        AND tu.fecha_solicitud < DATE_ADD(
                            p.fecha_fin,
                            INTERVAL 1 DAY
                        )
                        AND tu.estado IN (
                            'pendiente_aprobacion',
                            'observada',
                            'confirmada',
                            'realizada'
                        )
                ) AS cupos_ocupados
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            INNER JOIN periodos_inscripcion AS p
                ON p.id_periodo = :id_periodo
            LEFT JOIN tutor_periodo AS tp
                ON tp.id_tutor = t.id_tutor
                AND tp.id_periodo = p.id_periodo
            WHERE
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR u.usuario LIKE :buscar_usuario
                OR t.especialidad LIKE :buscar_especialidad
            ORDER BY u.nombre ASC, u.apellido ASC
        ";

        $patron = '%' . trim($busqueda) . '%';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'id_periodo' => $idPeriodo,
            'buscar_nombre' => $patron,
            'buscar_apellido' => $patron,
            'buscar_usuario' => $patron,
            'buscar_especialidad' => $patron
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Busca la configuración de un tutor en un periodo. */
    public function buscarConfiguracion(
        int $idTutor,
        int $idPeriodo
    ): ?array {
        $sql = "
            SELECT
                id_tutor_periodo,
                id_tutor,
                id_periodo,
                cupo_maximo,
                activo
            FROM tutor_periodo
            WHERE id_tutor = :id_tutor
                AND id_periodo = :id_periodo
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'id_tutor' => $idTutor,
            'id_periodo' => $idPeriodo
        ]);

        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado ?: null;
    }

    /** Comprueba que exista el perfil de tutor. */
    public function existeTutor(int $idTutor): bool
    {
        $consulta = $this->conexion->prepare(
            'SELECT COUNT(*) FROM tutores WHERE id_tutor = :id_tutor'
        );
        $consulta->execute(['id_tutor' => $idTutor]);

        return (int) $consulta->fetchColumn() === 1;
    }

    /** Guarda la configuración; el requisito vigente fija un máximo de cinco. */
    public function guardar(
        int $idTutor,
        int $idPeriodo,
        int $cupoMaximo,
        bool $activo
    ): bool {
        if ($cupoMaximo < 1 || $cupoMaximo > 5) {
            return false;
        }

        $sql = "
            INSERT INTO tutor_periodo (
                id_tutor,
                id_periodo,
                cupo_maximo,
                activo
            )
            VALUES (
                :id_tutor,
                :id_periodo,
                :cupo_maximo,
                :activo
            )
            ON DUPLICATE KEY UPDATE
                cupo_maximo = VALUES(cupo_maximo),
                activo = VALUES(activo)
        ";

        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'id_tutor' => $idTutor,
            'id_periodo' => $idPeriodo,
            'cupo_maximo' => $cupoMaximo,
            'activo' => $activo ? 1 : 0
        ]);
    }

    /** Cuenta estudiantes distintos con solicitudes vigentes en el periodo. */
    public function contarOcupados(
        int $idTutor,
        int $idPeriodo
    ): int {
        $sql = "
            SELECT COUNT(DISTINCT tu.id_estudiante)
            FROM tutorias AS tu
            INNER JOIN periodos_inscripcion AS p
                ON p.id_periodo = :id_periodo
            WHERE tu.id_tutor = :id_tutor
                AND tu.fecha_solicitud >= p.fecha_inicio
                AND tu.fecha_solicitud < DATE_ADD(
                    p.fecha_fin,
                    INTERVAL 1 DAY
                )
                AND tu.estado IN (
                    'pendiente_aprobacion',
                    'observada',
                    'confirmada',
                    'realizada'
                )
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'id_periodo' => $idPeriodo,
            'id_tutor' => $idTutor
        ]);

        return (int) $consulta->fetchColumn();
    }

    /** Comprueba si el tutor tiene espacio dentro de su configuración. */
    public function tieneCupoDisponible(
        int $idTutor,
        int $idPeriodo
    ): bool {
        $configuracion = $this->buscarConfiguracion(
            $idTutor,
            $idPeriodo
        );

        if (
            !$configuracion
            || (int) $configuracion['activo'] !== 1
        ) {
            return false;
        }

        return $this->contarOcupados($idTutor, $idPeriodo)
            < (int) $configuracion['cupo_maximo'];
    }

    /** Resume configuraciones y ocupación del periodo. */
    public function obtenerResumen(int $idPeriodo): array
    {
        $sql = "
            SELECT
                COUNT(*) AS tutores_configurados,
                COALESCE(SUM(activo = 1), 0) AS tutores_habilitados,
                COALESCE(
                    SUM(CASE WHEN activo = 1 THEN cupo_maximo ELSE 0 END),
                    0
                ) AS cupos_totales
            FROM tutor_periodo
            WHERE id_periodo = :id_periodo
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id_periodo' => $idPeriodo]);
        $resumen = $consulta->fetch(PDO::FETCH_ASSOC) ?: [];

        $consultaOcupados = $this->conexion->prepare(" 
            SELECT
                tp.id_tutor,
                COUNT(DISTINCT tu.id_estudiante) AS ocupados
            FROM tutor_periodo AS tp
            INNER JOIN periodos_inscripcion AS p
                ON p.id_periodo = tp.id_periodo
            LEFT JOIN tutorias AS tu
                ON tu.id_tutor = tp.id_tutor
                AND tu.fecha_solicitud >= p.fecha_inicio
                AND tu.fecha_solicitud < DATE_ADD(
                    p.fecha_fin,
                    INTERVAL 1 DAY
                )
                AND tu.estado IN (
                    'pendiente_aprobacion',
                    'observada',
                    'confirmada',
                    'realizada'
                )
            WHERE tp.id_periodo = :id_periodo
            GROUP BY tp.id_tutor
        ");
        $consultaOcupados->execute(['id_periodo' => $idPeriodo]);

        $cuposOcupados = 0;
        foreach ($consultaOcupados->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $cuposOcupados += (int) $fila['ocupados'];
        }

        $cuposTotales = (int) ($resumen['cupos_totales'] ?? 0);

        return [
            'tutores_configurados' => (int) (
                $resumen['tutores_configurados'] ?? 0
            ),
            'tutores_habilitados' => (int) (
                $resumen['tutores_habilitados'] ?? 0
            ),
            'cupos_totales' => $cuposTotales,
            'cupos_ocupados' => $cuposOcupados,
            'cupos_disponibles' => max(0, $cuposTotales - $cuposOcupados)
        ];
    }
}
