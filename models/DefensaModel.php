<?php

class DefensaModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las defensas con los datos del estudiante.
     */
    public function listar(
        string $busqueda = ''
    ): array {
        $sql = "
            SELECT
                d.id_defensa,
                d.id_expediente,
                d.etapa,
                d.fecha,
                d.hora_inicio,
                d.hora_fin,
                d.ambiente,
                d.estado,
                d.autorizado_por,
                d.referencia_autorizacion,

                e.titulo_trabajo,

                u.nombre,
                u.apellido,

                m.nombre AS modalidad,
                c.nombre AS cohorte

            FROM defensas_mg AS d

            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            WHERE (
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR COALESCE(
                    e.titulo_trabajo,
                    ''
                ) LIKE :buscar_titulo
                OR d.ambiente LIKE :buscar_ambiente
            )

            ORDER BY
                d.fecha DESC,
                d.hora_inicio ASC
        ";

        $patron = '%' . trim($busqueda) . '%';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'buscar_nombre' => $patron,
            'buscar_apellido' => $patron,
            'buscar_titulo' => $patron,
            'buscar_ambiente' => $patron
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene una defensa mediante su identificador.
     */
    public function buscarPorId(
        int $idDefensa
    ): ?array {
        $sql = "
            SELECT
                d.*,

                e.id_estudiante,
                e.titulo_trabajo,
                e.estado AS estado_expediente,

                u.nombre,
                u.apellido,

                m.nombre AS modalidad,
                c.nombre AS cohorte

            FROM defensas_mg AS d

            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            WHERE d.id_defensa = :id_defensa
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_defensa' => $idDefensa
        ]);

        $defensa = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $defensa ?: null;
    }

    /**
     * Lista expedientes que todavía no poseen una defensa vigente
     * para su etapa actual.
     */
    public function listarExpedientesDisponibles(): array
    {
        $sql = "
            SELECT
                e.id_expediente,
                e.etapa_actual,
                e.titulo_trabajo,

                u.nombre,
                u.apellido,

                m.nombre AS modalidad,
                c.nombre AS cohorte,

                COUNT(td.id_tribunal) AS tribunales_asignados

            FROM expedientes_mg AS e

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            LEFT JOIN tribunales_defensa AS td
                ON td.id_expediente = e.id_expediente
                AND td.etapa = e.etapa_actual
                AND td.estado = 'vigente'
                AND td.asignacion_vigente = 1

            WHERE e.estado = 'activo'
                AND e.etapa_actual IN ('mg1', 'mg2')
                AND NOT EXISTS (
                    SELECT 1
                    FROM defensas_mg AS d
                    WHERE d.id_expediente = e.id_expediente
                        AND d.etapa = e.etapa_actual
                        AND d.defensa_vigente = 1
                        AND d.estado IN (
                            'programada',
                            'realizada'
                        )
                )

            GROUP BY
                e.id_expediente,
                e.etapa_actual,
                e.titulo_trabajo,
                u.nombre,
                u.apellido,
                m.nombre,
                c.nombre

            ORDER BY
                u.nombre ASC,
                u.apellido ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene los datos de un expediente disponible.
     */
    public function buscarExpediente(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                e.id_expediente,
                e.id_estudiante,
                e.etapa_actual,
                e.estado,
                e.titulo_trabajo,

                u.nombre,
                u.apellido,

                m.nombre AS modalidad,
                c.nombre AS cohorte

            FROM expedientes_mg AS e

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            WHERE e.id_expediente = :id_expediente
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente
        ]);

        $expediente = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $expediente ?: null;
    }

    /**
     * Cuenta los tribunales vigentes de una etapa.
     */
    public function contarTribunales(
        int $idExpediente,
        string $etapa
    ): int {
        $sql = "
            SELECT COUNT(*)
            FROM tribunales_defensa
            WHERE id_expediente = :id_expediente
                AND etapa = :etapa
                AND estado = 'vigente'
                AND asignacion_vigente = 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Comprueba cruces de ambiente, estudiante o tribunal.
     *
     * Devuelve el tipo de conflicto encontrado o null.
     */
    public function buscarConflicto(
        int $idExpediente,
        string $etapa,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        string $ambiente,
        ?int $idDefensaExcluir = null
    ): ?string {
        $filtroExclusion = '';
        $parametrosBase = [
            'fecha' => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin
        ];

        if ($idDefensaExcluir !== null) {
            $filtroExclusion = "
                AND d.id_defensa != :id_defensa_excluir
            ";

            $parametrosBase['id_defensa_excluir']
                = $idDefensaExcluir;
        }

        // Revisamos si el ambiente ya está ocupado.
        $sqlAmbiente = "
            SELECT COUNT(*)
            FROM defensas_mg AS d
            WHERE d.fecha = :fecha
                AND d.ambiente = :ambiente
                AND d.estado IN (
                    'programada',
                    'realizada'
                )
                AND (
                    :hora_inicio < d.hora_fin
                    AND :hora_fin > d.hora_inicio
                )
                {$filtroExclusion}
        ";

        $consulta = $this->conexion->prepare(
            $sqlAmbiente
        );

        $consulta->execute(
            array_merge(
                $parametrosBase,
                ['ambiente' => $ambiente]
            )
        );

        if ((int) $consulta->fetchColumn() > 0) {
            return 'ambiente';
        }

        // Revisamos si el estudiante tiene otra defensa.
        $sqlEstudiante = "
            SELECT COUNT(*)

            FROM defensas_mg AS d

            INNER JOIN expedientes_mg AS e_existente
                ON e_existente.id_expediente = d.id_expediente

            INNER JOIN expedientes_mg AS e_actual
                ON e_actual.id_expediente = :id_expediente

            WHERE d.fecha = :fecha
                AND e_existente.id_estudiante
                    = e_actual.id_estudiante
                AND d.estado IN (
                    'programada',
                    'realizada'
                )
                AND (
                    :hora_inicio < d.hora_fin
                    AND :hora_fin > d.hora_inicio
                )
                {$filtroExclusion}
        ";

        $consulta = $this->conexion->prepare(
            $sqlEstudiante
        );

        $consulta->execute(
            array_merge(
                $parametrosBase,
                ['id_expediente' => $idExpediente]
            )
        );

        if ((int) $consulta->fetchColumn() > 0) {
            return 'estudiante';
        }

        // Revisamos si algún tribunal participa en otra defensa.
        $sqlTribunal = "
            SELECT COUNT(DISTINCT td_actual.id_tutor)

            FROM tribunales_defensa AS td_actual

            INNER JOIN tribunales_defensa AS td_existente
                ON td_existente.id_tutor = td_actual.id_tutor
                AND td_existente.estado = 'vigente'
                AND td_existente.asignacion_vigente = 1

            INNER JOIN defensas_mg AS d
                ON d.id_expediente = td_existente.id_expediente
                AND d.etapa = td_existente.etapa

            WHERE td_actual.id_expediente = :id_expediente
                AND td_actual.etapa = :etapa
                AND td_actual.estado = 'vigente'
                AND td_actual.asignacion_vigente = 1
                AND d.fecha = :fecha
                AND d.estado IN (
                    'programada',
                    'realizada'
                )
                AND (
                    :hora_inicio < d.hora_fin
                    AND :hora_fin > d.hora_inicio
                )
                {$filtroExclusion}
        ";

        $consulta = $this->conexion->prepare(
            $sqlTribunal
        );

        $consulta->execute(
            array_merge(
                $parametrosBase,
                [
                    'id_expediente' => $idExpediente,
                    'etapa' => $etapa
                ]
            )
        );

        if ((int) $consulta->fetchColumn() > 0) {
            return 'tribunal';
        }

        return null;
    }

    /**
     * Programa una defensa.
     */
    public function crear(
        int $idExpediente,
        string $etapa,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        string $ambiente,
        ?string $autorizadoPor,
        ?string $referenciaAutorizacion,
        int $registradoPor
    ): bool {
        $sql = "
            INSERT INTO defensas_mg (
                id_expediente,
                etapa,
                fecha,
                hora_inicio,
                hora_fin,
                ambiente,
                autorizado_por,
                referencia_autorizacion,
                registrado_por
            )
            VALUES (
                :id_expediente,
                :etapa,
                :fecha,
                :hora_inicio,
                :hora_fin,
                :ambiente,
                :autorizado_por,
                :referencia_autorizacion,
                :registrado_por
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa,
            'fecha' => $fecha,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'ambiente' => trim($ambiente),
            'autorizado_por' => $autorizadoPor,
            'referencia_autorizacion'
                => $referenciaAutorizacion,
            'registrado_por' => $registradoPor
        ]);
    }
}