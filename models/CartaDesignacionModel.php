<?php

class CartaDesignacionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las cartas relacionadas con la cuenta del tutor.
     */
    public function listarPorTutor(
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                cd.id_carta,
                cd.numero_carta,
                cd.version_carta,
                cd.estado,
                cd.fecha_generacion,
                cd.fecha_respuesta,
                cd.tipo_rechazo,
                cd.motivo_rechazo,
                cd.responsabilidades,
                cd.documento_url,

                at.id_asignacion,
                at.estado AS estado_asignacion,

                em.id_expediente,
                em.etapa_actual,
                em.titulo_trabajo,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,

                mg.nombre AS nombre_modalidad,
                cm.nombre AS nombre_cohorte

            FROM cartas_designacion_mg AS cd

            INNER JOIN asignaciones_tutor AS at
                ON at.id_asignacion = cd.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = at.id_tutor

            INNER JOIN expedientes_mg AS em
                ON em.id_expediente = at.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = em.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS mg
                ON mg.id_modalidad = em.id_modalidad

            INNER JOIN cohortes_mg AS cm
                ON cm.id_cohorte = em.id_cohorte

            WHERE t.id_usuario = :id_usuario

            ORDER BY
                cd.fecha_generacion DESC,
                cd.id_carta DESC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Busca una carta verificando que pertenezca al tutor autenticado.
     */
    public function buscarPorIdParaTutor(
        int $idCarta,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                cd.id_carta,
                cd.numero_carta,
                cd.version_carta,
                cd.estado,
                cd.fecha_generacion,
                cd.fecha_respuesta,
                cd.tipo_rechazo,
                cd.motivo_rechazo,
                cd.responsabilidades,
                cd.documento_url,

                at.id_asignacion,
                at.id_expediente,
                at.id_tutor,
                at.estado AS estado_asignacion,

                em.etapa_actual,
                em.estado AS estado_expediente,
                em.titulo_trabajo,
                em.observaciones,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,
                ue.correo AS correo_estudiante,

                mg.nombre AS nombre_modalidad,
                cm.nombre AS nombre_cohorte

            FROM cartas_designacion_mg AS cd

            INNER JOIN asignaciones_tutor AS at
                ON at.id_asignacion = cd.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = at.id_tutor

            INNER JOIN expedientes_mg AS em
                ON em.id_expediente = at.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = em.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS mg
                ON mg.id_modalidad = em.id_modalidad

            INNER JOIN cohortes_mg AS cm
                ON cm.id_cohorte = em.id_cohorte

            WHERE cd.id_carta = :id_carta
                AND t.id_usuario = :id_usuario

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_carta' => $idCarta,
            'id_usuario' => $idUsuario
        ]);

        $carta = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $carta ?: null;
    }

    /**
     * Registra la aceptación o el rechazo de una carta.
     *
     * Si se rechaza, la asignación termina y el expediente vuelve
     * a la etapa previa para permitir una nueva designación.
     */
    public function responder(
        int $idCarta,
        int $idUsuario,
        string $respuesta,
        ?string $tipoRechazo = null,
        ?string $motivoRechazo = null
    ): bool {
        if (
            !in_array(
                $respuesta,
                ['aceptada', 'rechazada'],
                true
            )
        ) {
            throw new InvalidArgumentException(
                'La respuesta seleccionada no es válida.'
            );
        }

        $tiposPermitidos = [
            'sin_capacidad',
            'sin_tiempo',
            'no_corresponde',
            'otro'
        ];

        if ($respuesta === 'rechazada') {
            if (
                $tipoRechazo === null
                || !in_array(
                    $tipoRechazo,
                    $tiposPermitidos,
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Selecciona un motivo de rechazo válido.'
                );
            }

            if (
                $motivoRechazo === null
                || trim($motivoRechazo) === ''
            ) {
                throw new InvalidArgumentException(
                    'Explica brevemente el motivo del rechazo.'
                );
            }
        }

        try {
            $this->conexion->beginTransaction();

            // Bloqueamos la carta para impedir respuestas simultáneas.
            $sqlCarta = "
                SELECT
                    cd.id_carta,
                    cd.estado,
                    at.id_asignacion,
                    at.id_expediente,
                    at.estado AS estado_asignacion
                FROM cartas_designacion_mg AS cd
                INNER JOIN asignaciones_tutor AS at
                    ON at.id_asignacion = cd.id_asignacion
                INNER JOIN tutores AS t
                    ON t.id_tutor = at.id_tutor
                WHERE cd.id_carta = :id_carta
                    AND t.id_usuario = :id_usuario
                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaCarta = $this->conexion->prepare(
                $sqlCarta
            );

            $sentenciaCarta->execute([
                'id_carta' => $idCarta,
                'id_usuario' => $idUsuario
            ]);

            $carta = $sentenciaCarta->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$carta) {
                throw new RuntimeException(
                    'La carta solicitada no existe o no te pertenece.'
                );
            }

            if ($carta['estado'] !== 'pendiente') {
                throw new RuntimeException(
                    'Esta carta ya fue respondida anteriormente.'
                );
            }

            if ($carta['estado_asignacion'] !== 'vigente') {
                throw new RuntimeException(
                    'La asignación relacionada ya no está vigente.'
                );
            }

            $sqlRespuesta = "
                UPDATE cartas_designacion_mg
                SET
                    estado = :estado,
                    fecha_respuesta = NOW(),
                    tipo_rechazo = :tipo_rechazo,
                    motivo_rechazo = :motivo_rechazo
                WHERE id_carta = :id_carta
                    AND estado = 'pendiente'
            ";

            $sentenciaRespuesta = $this->conexion->prepare(
                $sqlRespuesta
            );

            $sentenciaRespuesta->execute([
                'estado' => $respuesta,
                'tipo_rechazo' => $respuesta === 'rechazada'
                    ? $tipoRechazo
                    : null,
                'motivo_rechazo' => $respuesta === 'rechazada'
                    ? trim((string) $motivoRechazo)
                    : null,
                'id_carta' => $idCarta
            ]);

            if ($sentenciaRespuesta->rowCount() !== 1) {
                throw new RuntimeException(
                    'No fue posible registrar la respuesta.'
                );
            }

            if ($respuesta === 'rechazada') {
                $idAsignacion = (int) $carta['id_asignacion'];
                $idExpediente = (int) $carta['id_expediente'];
                $motivo = trim((string) $motivoRechazo);

                // Cerramos la asignación sin eliminar su historial.
                $sqlAsignacion = "
                    UPDATE asignaciones_tutor
                    SET
                        estado = 'finalizada',
                        fecha_fin = NOW(),
                        motivo_fin = :motivo_fin,
                        asignacion_vigente = NULL
                    WHERE id_asignacion = :id_asignacion
                        AND estado = 'vigente'
                ";

                $sentenciaAsignacion = $this->conexion->prepare(
                    $sqlAsignacion
                );

                $sentenciaAsignacion->execute([
                    'motivo_fin' => 'Designación rechazada: '
                        . $motivo,
                    'id_asignacion' => $idAsignacion
                ]);

                // Cerramos MG1 porque el tutor no aceptó la designación.
                $sqlCerrarEtapa = "
                    UPDATE expediente_etapas
                    SET
                        fecha_fin = CURDATE(),
                        resultado = 'interrumpida',
                        motivo_cierre = :motivo_cierre
                    WHERE id_expediente = :id_expediente
                        AND etapa = 'mg1'
                        AND fecha_fin IS NULL
                ";

                $sentenciaCerrarEtapa = $this->conexion->prepare(
                    $sqlCerrarEtapa
                );

                $sentenciaCerrarEtapa->execute([
                    'motivo_cierre' => 'El tutor rechazó la carta de designación.',
                    'id_expediente' => $idExpediente
                ]);

                // El expediente vuelve a previa para poder reasignarlo.
                $sqlExpediente = "
                    UPDATE expedientes_mg
                    SET etapa_actual = 'previa'
                    WHERE id_expediente = :id_expediente
                        AND estado = 'activo'
                ";

                $sentenciaExpediente = $this->conexion->prepare(
                    $sqlExpediente
                );

                $sentenciaExpediente->execute([
                    'id_expediente' => $idExpediente
                ]);

                // Abrimos un nuevo registro de etapa previa.
                $sqlEtapaPrevia = "
                    INSERT INTO expediente_etapas (
                        id_expediente,
                        etapa,
                        fecha_inicio,
                        registrado_por
                    )
                    VALUES (
                        :id_expediente,
                        'previa',
                        CURDATE(),
                        :registrado_por
                    )
                ";

                $sentenciaEtapaPrevia = $this->conexion->prepare(
                    $sqlEtapaPrevia
                );

                $sentenciaEtapaPrevia->execute([
                    'id_expediente' => $idExpediente,
                    'registrado_por' => $idUsuario
                ]);
            }

            $this->conexion->commit();

            return true;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }
}