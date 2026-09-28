<?php

class InformeAvanceModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los informes de un expediente asignado al tutor.
     */
    public function listarPorExpediente(
        int $idExpediente,
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                i.id_informe,
                i.id_expediente,
                i.id_hito,
                i.etapa,
                i.numero_informe,
                i.fecha_informe,
                i.porcentaje_avance,
                i.resumen_avance,
                i.logros,
                i.dificultades,
                i.recomendaciones,
                i.proximas_actividades,
                i.estado,
                i.observacion_revision,
                i.fecha_revision,
                i.fecha_registro,
                i.fecha_actualizacion,

                h.nombre AS nombre_hito,
                h.fecha_limite,

                ur.nombre AS nombre_revisor,
                ur.apellido AS apellido_revisor

            FROM informes_avance_mg AS i

            LEFT JOIN calendario_mg AS h
                ON h.id_hito = i.id_hito

            LEFT JOIN usuarios AS ur
                ON ur.id_usuario = i.revisado_por

            WHERE i.id_expediente = :id_expediente

                AND EXISTS (
                    SELECT 1
                    FROM asignaciones_tutor AS a

                    INNER JOIN tutores AS t
                        ON t.id_tutor = a.id_tutor

                    INNER JOIN cartas_designacion_mg AS c
                        ON c.id_asignacion = a.id_asignacion
                        AND c.estado = 'aceptada'

                    WHERE a.id_expediente = i.id_expediente
                        AND t.id_usuario = :id_usuario
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                )

            ORDER BY
                i.etapa ASC,
                i.numero_informe ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene los hitos de informe disponibles para el expediente.
     */
    public function listarHitosDisponibles(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                h.id_hito,
                h.etapa,
                h.nombre,
                h.fecha_limite,
                h.avance_esperado_pct

            FROM calendario_mg AS h

            INNER JOIN expedientes_mg AS x
                ON x.id_cohorte = h.id_cohorte

            WHERE x.id_expediente = :id_expediente
                AND h.tipo = 'informe'

            ORDER BY
                h.fecha_limite ASC,
                h.orden ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Calcula el siguiente número de informe dentro de la etapa.
     */
    public function obtenerSiguienteNumero(
        int $idExpediente,
        string $etapa
    ): int {
        $sql = "
            SELECT
                COALESCE(
                    MAX(numero_informe),
                    0
                ) + 1
            FROM informes_avance_mg
            WHERE id_expediente = :id_expediente
                AND etapa = :etapa
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa
        ]);

        return (int) $sentencia->fetchColumn();
    }

    /**
     * Busca un informe verificando que pertenezca al tutor.
     */
    public function buscarPorIdParaTutor(
        int $idInforme,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                i.*,

                x.titulo_trabajo,
                x.etapa_actual,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,

                h.nombre AS nombre_hito,
                h.fecha_limite

            FROM informes_avance_mg AS i

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = i.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            LEFT JOIN calendario_mg AS h
                ON h.id_hito = i.id_hito

            WHERE i.id_informe = :id_informe

                AND EXISTS (
                    SELECT 1
                    FROM asignaciones_tutor AS a

                    INNER JOIN tutores AS t
                        ON t.id_tutor = a.id_tutor

                    INNER JOIN cartas_designacion_mg AS c
                        ON c.id_asignacion = a.id_asignacion
                        AND c.estado = 'aceptada'

                    WHERE a.id_expediente = i.id_expediente
                        AND t.id_usuario = :id_usuario
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                )

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_informe' => $idInforme,
            'id_usuario' => $idUsuario
        ]);

        $informe = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $informe ?: null;
    }

    /**
     * Crea un informe dentro de la etapa actual del expediente.
     */
    public function crear(
        int $idExpediente,
        int $idUsuario,
        ?int $idHito,
        int $numeroInforme,
        string $fechaInforme,
        int $porcentajeAvance,
        string $resumenAvance,
        ?string $logros,
        ?string $dificultades,
        ?string $recomendaciones,
        ?string $proximasActividades
    ): int {
        try {
            $this->conexion->beginTransaction();

            // Bloqueamos el expediente y confirmamos la asignación.
            $sqlExpediente = "
                SELECT
                    x.etapa_actual,
                    x.estado

                FROM expedientes_mg AS x

                WHERE x.id_expediente = :id_expediente

                    AND EXISTS (
                        SELECT 1
                        FROM asignaciones_tutor AS a

                        INNER JOIN tutores AS t
                            ON t.id_tutor = a.id_tutor

                        INNER JOIN cartas_designacion_mg AS c
                            ON c.id_asignacion = a.id_asignacion
                            AND c.estado = 'aceptada'

                        WHERE a.id_expediente = x.id_expediente
                            AND t.id_usuario = :id_usuario
                            AND a.estado = 'vigente'
                            AND a.asignacion_vigente = 1
                    )

                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaExpediente = $this->conexion->prepare(
                $sqlExpediente
            );

            $sentenciaExpediente->execute([
                'id_expediente' => $idExpediente,
                'id_usuario' => $idUsuario
            ]);

            $expediente = $sentenciaExpediente->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$expediente) {
                throw new RuntimeException(
                    'No tienes acceso a este expediente.'
                );
            }

            if ($expediente['estado'] !== 'activo') {
                throw new RuntimeException(
                    'El expediente ya no está activo.'
                );
            }

            $etapa = $expediente['etapa_actual'];

            if (
                !in_array(
                    $etapa,
                    ['mg1', 'mg2'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'La etapa actual no permite registrar informes.'
                );
            }

            // El hito seleccionado debe pertenecer a la misma cohorte y etapa.
            if ($idHito !== null) {
                $sqlHito = "
                    SELECT COUNT(*)
                    FROM calendario_mg AS h

                    INNER JOIN expedientes_mg AS x
                        ON x.id_cohorte = h.id_cohorte

                    WHERE h.id_hito = :id_hito
                        AND x.id_expediente = :id_expediente
                        AND h.tipo = 'informe'
                        AND h.etapa = :etapa
                ";

                $sentenciaHito = $this->conexion->prepare(
                    $sqlHito
                );

                $sentenciaHito->execute([
                    'id_hito' => $idHito,
                    'id_expediente' => $idExpediente,
                    'etapa' => $etapa
                ]);

                if (
                    (int) $sentenciaHito->fetchColumn() !== 1
                ) {
                    throw new RuntimeException(
                        'El hito seleccionado no corresponde al expediente.'
                    );
                }
            }

            $sql = "
                INSERT INTO informes_avance_mg (
                    id_expediente,
                    id_hito,
                    etapa,
                    numero_informe,
                    fecha_informe,
                    porcentaje_avance,
                    resumen_avance,
                    logros,
                    dificultades,
                    recomendaciones,
                    proximas_actividades,
                    registrado_por
                )
                VALUES (
                    :id_expediente,
                    :id_hito,
                    :etapa,
                    :numero_informe,
                    :fecha_informe,
                    :porcentaje_avance,
                    :resumen_avance,
                    :logros,
                    :dificultades,
                    :recomendaciones,
                    :proximas_actividades,
                    :registrado_por
                )
            ";

            $sentencia = $this->conexion->prepare($sql);

            $sentencia->execute([
                'id_expediente' => $idExpediente,
                'id_hito' => $idHito,
                'etapa' => $etapa,
                'numero_informe' => $numeroInforme,
                'fecha_informe' => $fechaInforme,
                'porcentaje_avance' => $porcentajeAvance,
                'resumen_avance' => $resumenAvance,
                'logros' => $logros,
                'dificultades' => $dificultades,
                'recomendaciones' => $recomendaciones,
                'proximas_actividades' => $proximasActividades,
                'registrado_por' => $idUsuario
            ]);

            $idInforme = (int) $this->conexion
                ->lastInsertId();

            $this->conexion->commit();

            return $idInforme;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Presenta un borrador para que coordinación pueda revisarlo.
     */
    public function presentar(
        int $idInforme,
        int $idUsuario
    ): bool {
        $sql = "
            UPDATE informes_avance_mg AS i

            SET
                i.estado = 'presentado',
                i.observacion_revision = NULL,
                i.revisado_por = NULL,
                i.fecha_revision = NULL

            WHERE i.id_informe = :id_informe
                AND i.estado IN (
                    'borrador',
                    'observado'
                )

                AND EXISTS (
                    SELECT 1
                    FROM asignaciones_tutor AS a

                    INNER JOIN tutores AS t
                        ON t.id_tutor = a.id_tutor

                    INNER JOIN cartas_designacion_mg AS c
                        ON c.id_asignacion = a.id_asignacion
                        AND c.estado = 'aceptada'

                    WHERE a.id_expediente = i.id_expediente
                        AND t.id_usuario = :id_usuario
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                )
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_informe' => $idInforme,
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->rowCount() === 1;
    }

    /**
 * Actualiza un informe mientras permanezca editable.
 */
public function actualizar(
    int $idInforme,
    int $idUsuario,
    ?int $idHito,
    string $fechaInforme,
    int $porcentajeAvance,
    string $resumenAvance,
    ?string $logros,
    ?string $dificultades,
    ?string $recomendaciones,
    ?string $proximasActividades
): bool {
    try {
        $this->conexion->beginTransaction();

        // Bloqueamos el informe y comprobamos su estado.
        $sqlInforme = "
            SELECT
                i.id_expediente,
                i.etapa,
                i.estado

            FROM informes_avance_mg AS i

            WHERE i.id_informe = :id_informe

                AND EXISTS (
                    SELECT 1
                    FROM asignaciones_tutor AS a

                    INNER JOIN tutores AS t
                        ON t.id_tutor = a.id_tutor

                    INNER JOIN cartas_designacion_mg AS c
                        ON c.id_asignacion = a.id_asignacion
                        AND c.estado = 'aceptada'

                    WHERE a.id_expediente = i.id_expediente
                        AND t.id_usuario = :id_usuario
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                )

            LIMIT 1
            FOR UPDATE
        ";

        $sentenciaInforme = $this->conexion->prepare(
            $sqlInforme
        );

        $sentenciaInforme->execute([
            'id_informe' => $idInforme,
            'id_usuario' => $idUsuario
        ]);

        $informe = $sentenciaInforme->fetch(
            PDO::FETCH_ASSOC
        );

        if (!$informe) {
            throw new RuntimeException(
                'No tienes acceso a este informe.'
            );
        }

        if (
            !in_array(
                $informe['estado'],
                ['borrador', 'observado'],
                true
            )
        ) {
            throw new RuntimeException(
                'El informe ya no puede modificarse.'
            );
        }

        if ($idHito !== null) {
            $sqlHito = "
                SELECT COUNT(*)
                FROM calendario_mg AS h

                INNER JOIN expedientes_mg AS x
                    ON x.id_cohorte = h.id_cohorte

                WHERE h.id_hito = :id_hito
                    AND x.id_expediente = :id_expediente
                    AND h.tipo = 'informe'
                    AND h.etapa = :etapa
            ";

            $sentenciaHito = $this->conexion->prepare(
                $sqlHito
            );

            $sentenciaHito->execute([
                'id_hito' => $idHito,
                'id_expediente' => $informe['id_expediente'],
                'etapa' => $informe['etapa']
            ]);

            if (
                (int) $sentenciaHito->fetchColumn() !== 1
            ) {
                throw new RuntimeException(
                    'El hito seleccionado no corresponde al informe.'
                );
            }
        }

        $sqlActualizar = "
            UPDATE informes_avance_mg
            SET
                id_hito = :id_hito,
                fecha_informe = :fecha_informe,
                porcentaje_avance = :porcentaje_avance,
                resumen_avance = :resumen_avance,
                logros = :logros,
                dificultades = :dificultades,
                recomendaciones = :recomendaciones,
                proximas_actividades = :proximas_actividades,
                estado = 'borrador',
                observacion_revision = NULL,
                revisado_por = NULL,
                fecha_revision = NULL
            WHERE id_informe = :id_informe
        ";

        $sentenciaActualizar = $this->conexion->prepare(
            $sqlActualizar
        );

        $resultado = $sentenciaActualizar->execute([
            'id_hito' => $idHito,
            'fecha_informe' => $fechaInforme,
            'porcentaje_avance' => $porcentajeAvance,
            'resumen_avance' => $resumenAvance,
            'logros' => $logros,
            'dificultades' => $dificultades,
            'recomendaciones' => $recomendaciones,
            'proximas_actividades' => $proximasActividades,
            'id_informe' => $idInforme
        ]);

        $this->conexion->commit();

        return $resultado;
    } catch (Throwable $error) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }

        throw $error;
    }
}
}