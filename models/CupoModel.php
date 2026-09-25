<?php

class CupoModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los tutores y su configuración dentro de un periodo.
     *
     * También calcula cuántos procesos activos tiene cada tutor.
     * Los procesos finalizados o cancelados no ocupan cupo.
     */
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
                    SELECT COUNT(*)
                    FROM tutorias AS tu
                    WHERE tu.id_tutor = t.id_tutor
                        AND tu.id_periodo = :periodo_ocupados
                        AND tu.estado NOT IN (
                            'cancelada',
                            'finalizada'
                        )
                ) AS cupos_ocupados
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            LEFT JOIN tutor_periodo AS tp
                ON tp.id_tutor = t.id_tutor
                AND tp.id_periodo = :periodo_configuracion
            WHERE (
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR u.usuario LIKE :buscar_usuario
                OR t.especialidad LIKE :buscar_especialidad
            )
            ORDER BY
                u.nombre ASC,
                u.apellido ASC
        ";

        $patronBusqueda = '%' . trim($busqueda) . '%';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'periodo_ocupados' => $idPeriodo,
            'periodo_configuracion' => $idPeriodo,
            'buscar_nombre' => $patronBusqueda,
            'buscar_apellido' => $patronBusqueda,
            'buscar_usuario' => $patronBusqueda,
            'buscar_especialidad' => $patronBusqueda
        ]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la configuración de un tutor en un periodo.
     */
    public function buscarConfiguracion(
        int $idTutor,
        int $idPeriodo
    ): ?array {
        $sql = "
            SELECT
                tp.id_tutor_periodo,
                tp.id_tutor,
                tp.id_periodo,
                tp.cupo_maximo,
                tp.activo
            FROM tutor_periodo AS tp
            WHERE tp.id_tutor = :id_tutor
                AND tp.id_periodo = :id_periodo
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

    /**
     * Crea o actualiza el cupo de un tutor dentro de un periodo.
     *
     * La combinación tutor-periodo debe ser única en la base de datos.
     */
    public function guardar(
        int $idTutor,
        int $idPeriodo,
        int $cupoMaximo,
        bool $activo
    ): bool {
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

    /**
     * Cuenta los procesos que actualmente ocupan cupo.
     */
    public function contarOcupados(
        int $idTutor,
        int $idPeriodo
    ): int {
        $sql = "
            SELECT COUNT(*)
            FROM tutorias
            WHERE id_tutor = :id_tutor
                AND id_periodo = :id_periodo
                AND estado NOT IN (
                    'cancelada',
                    'finalizada'
                )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_tutor' => $idTutor,
            'id_periodo' => $idPeriodo
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Comprueba si el tutor todavía puede recibir otro proceso.
     */
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

        $ocupados = $this->contarOcupados(
            $idTutor,
            $idPeriodo
        );

        return $ocupados
            < (int) $configuracion['cupo_maximo'];
    }

    /**
     * Obtiene un resumen general de los cupos del periodo.
     */
    public function obtenerResumen(int $idPeriodo): array
    {
        $sql = "
            SELECT
                COUNT(*) AS tutores_configurados,
                COALESCE(
                    SUM(
                        CASE
                            WHEN activo = 1
                            THEN 1
                            ELSE 0
                        END
                    ),
                    0
                ) AS tutores_habilitados,
                COALESCE(
                    SUM(
                        CASE
                            WHEN activo = 1
                            THEN cupo_maximo
                            ELSE 0
                        END
                    ),
                    0
                ) AS cupos_totales
            FROM tutor_periodo
            WHERE id_periodo = :id_periodo
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_periodo' => $idPeriodo
        ]);

        $resumen = $consulta->fetch(PDO::FETCH_ASSOC);

        $sqlOcupados = "
            SELECT COUNT(*)
            FROM tutorias
            WHERE id_periodo = :id_periodo
                AND id_tutor IS NOT NULL
                AND estado NOT IN (
                    'cancelada',
                    'finalizada'
                )
        ";

        $consultaOcupados = $this->conexion->prepare(
            $sqlOcupados
        );

        $consultaOcupados->execute([
            'id_periodo' => $idPeriodo
        ]);

        $cuposOcupados = (int) $consultaOcupados
            ->fetchColumn();

        $cuposTotales = (int) (
            $resumen['cupos_totales'] ?? 0
        );

        return [
            'tutores_configurados' => (int) (
                $resumen['tutores_configurados'] ?? 0
            ),
            'tutores_habilitados' => (int) (
                $resumen['tutores_habilitados'] ?? 0
            ),
            'cupos_totales' => $cuposTotales,
            'cupos_ocupados' => $cuposOcupados,
            'cupos_disponibles' => max(
                0,
                $cuposTotales - $cuposOcupados
            )
        ];
    }
}