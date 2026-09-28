<?php

class NotificacionModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Crea una notificación ligada a una transición confirmada. */
    public function crearParaUsuario(
        int $idUsuario,
        string $tipo,
        string $titulo,
        string $mensaje,
        string $urlDestino
    ): void {
        if (
            $idUsuario < 1
            || !preg_match('/^[a-z0-9_]{1,60}$/', $tipo)
            || !str_starts_with($urlDestino, 'controllers/')
            || str_contains($urlDestino, '..')
            || str_contains($urlDestino, '://')
        ) {
            throw new InvalidArgumentException(
                'Los datos de la notificación no son válidos.'
            );
        }

        $consulta = $this->conexion->prepare("
            INSERT INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            ) VALUES (
                :id_usuario,
                :tipo,
                :titulo,
                :mensaje,
                :url_destino,
                :clave_evento
            )
        ");

        $consulta->execute([
            'id_usuario' => $idUsuario,
            'tipo' => $tipo,
            'titulo' => mb_substr($titulo, 0, 150),
            'mensaje' => mb_substr($mensaje, 0, 500),
            'url_destino' => mb_substr($urlDestino, 0, 255),
            'clave_evento' => 'evento:' . bin2hex(random_bytes(24))
        ]);
    }

    /**
     * Genera notificaciones pendientes a partir de eventos reales.
     *
     * INSERT IGNORE y clave_evento impiden duplicarlas.
     */
    public function sincronizar(): void
    {
        // Asignaciones y cartas pendientes para tutores
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                t.id_usuario,
                'asignacion_tutor',
                'Nueva asignación académica',
                CONCAT(
                    'Tienes una carta de asignación pendiente para el expediente de ',
                    ue.nombre,
                    ' ',
                    ue.apellido,
                    '.'
                ),
                CONCAT(
                    'controllers/carta_tutor_ver.php?id=',
                    c.id_carta
                ),
                CONCAT(
                    'carta_tutor:',
                    c.id_carta
                )
            FROM cartas_designacion_mg AS c
            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = c.id_asignacion
            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = a.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS ue
                ON ue.id_usuario = es.id_usuario
            WHERE c.estado = 'pendiente'
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
        ";

        $this->conexion->exec($sql);

        // Defensa programada para el estudiante
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                es.id_usuario,
                'defensa_programada',
                'Defensa programada',
                CONCAT(
                    'Tu defensa de ',
                    UPPER(d.etapa),
                    ' fue programada para el ',
                    DATE_FORMAT(d.fecha, '%d/%m/%Y'),
                    ' a horas ',
                    TIME_FORMAT(d.hora_inicio, '%H:%i'),
                    '.'
                ),
                'controllers/mi_proceso_listar.php',
                CONCAT(
                    'defensa_estudiante:',
                    d.id_defensa
                )
            FROM defensas_mg AS d
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            WHERE d.estado = 'programada'
                AND d.defensa_vigente = 1
        ";

        $this->conexion->exec($sql);

        // Defensa programada para cada tribunal
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                t.id_usuario,
                'tribunal_defensa',
                'Participación en defensa',
                CONCAT(
                    'Fuiste asignado como Tribunal ',
                    td.orden,
                    ' para una defensa de ',
                    UPPER(d.etapa),
                    ' programada el ',
                    DATE_FORMAT(d.fecha, '%d/%m/%Y'),
                    '.'
                ),
                CONCAT(
                    'controllers/documento_ver.php?id=',
                    COALESCE(
                        (
                            SELECT dg.id_documento
                            FROM documentos_generados AS dg
                            WHERE dg.id_defensa = d.id_defensa
                                AND dg.tipo = 'citacion_tribunal'
                                AND dg.destinatario
                                    COLLATE utf8mb4_unicode_ci
                                    = CONCAT(
                                        ut.nombre,
                                        ' ',
                                        ut.apellido
                                    ) COLLATE utf8mb4_unicode_ci
                            LIMIT 1
                        ),
                        0
                    )
                ),
                CONCAT(
                    'defensa_tribunal:',
                    d.id_defensa,
                    ':',
                    td.id_tutor
                )
            FROM defensas_mg AS d
            INNER JOIN tribunales_defensa AS td
                ON td.id_expediente = d.id_expediente
                AND td.etapa = d.etapa
                AND td.estado = 'vigente'
                AND td.asignacion_vigente = 1
            INNER JOIN tutores AS t
                ON t.id_tutor = td.id_tutor
            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario
            WHERE d.estado = 'programada'
                AND d.defensa_vigente = 1
        ";

        $this->conexion->exec($sql);

        // Informes observados para el tutor vigente
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                t.id_usuario,
                'informe_observado',
                'Informe observado',
                CONCAT(
                    'El informe ',
                    i.numero_informe,
                    ' de ',
                    UPPER(i.etapa),
                    ' requiere correcciones.'
                ),
                CONCAT(
                    'controllers/informe_ver.php?id=',
                    i.id_informe
                ),
                CONCAT(
                    'informe_observado:',
                    i.id_informe,
                    ':',
                    UNIX_TIMESTAMP(i.fecha_actualizacion)
                )
            FROM informes_avance_mg AS i
            INNER JOIN asignaciones_tutor AS a
                ON a.id_expediente = i.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor
            WHERE i.estado = 'observado'
        ";

        $this->conexion->exec($sql);

        // Reuniones observadas para quien las registró
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                r.registrado_por,
                'reunion_observada',
                'Reunión observada',
                CONCAT(
                    'La reunión del ',
                    DATE_FORMAT(r.fecha_reunion, '%d/%m/%Y'),
                    ' requiere revisión.'
                ),
                CONCAT(
                    'controllers/reunion_ver.php?id=',
                    r.id_reunion
                ),
                CONCAT(
                    'reunion_observada:',
                    r.id_reunion,
                    ':',
                    UNIX_TIMESTAMP(r.fecha_actualizacion)
                )
            FROM reuniones_mg AS r
            WHERE r.estado_validacion = 'observada'
        ";

        $this->conexion->exec($sql);

        // Calificaciones publicadas para estudiantes
        $sql = "
            INSERT IGNORE INTO notificaciones (
                id_usuario,
                tipo,
                titulo,
                mensaje,
                url_destino,
                clave_evento
            )
            SELECT
                es.id_usuario,
                'calificacion_publicada',
                'Calificación publicada',
                CONCAT(
                    'Tu calificación de ',
                    UPPER(d.etapa),
                    ' ya se encuentra disponible.'
                ),
                'controllers/mis_calificaciones.php',
                CONCAT(
                    'calificacion:',
                    c.id_calificacion,
                    ':',
                    UNIX_TIMESTAMP(c.fecha_actualizacion)
                )
            FROM calificaciones_mg AS c
            INNER JOIN defensas_mg AS d
                ON d.id_defensa = c.id_defensa
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            WHERE c.publicada = 1
        ";

        $this->conexion->exec($sql);
    }

    /**
     * Lista las notificaciones del usuario autenticado.
     */
    public function listarPorUsuario(
        int $idUsuario,
        int $limite = 50
    ): array {
        $limite = max(
            1,
            min($limite, 100)
        );

        $sql = "
            SELECT
                id_notificacion,
                tipo,
                titulo,
                mensaje,
                url_destino,
                leida,
                fecha_registro,
                fecha_lectura
            FROM notificaciones
            WHERE id_usuario = :id_usuario
            ORDER BY
                leida ASC,
                fecha_registro DESC
            LIMIT {$limite}
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Cuenta las notificaciones pendientes.
     */
    public function contarNoLeidas(
        int $idUsuario
    ): int {
        $sql = "
            SELECT COUNT(*)
            FROM notificaciones
            WHERE id_usuario = :id_usuario
                AND leida = 0
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Marca como leída únicamente una notificación del usuario.
     */
    public function marcarLeida(
        int $idNotificacion,
        int $idUsuario
    ): ?string {
        $sqlBuscar = "
            SELECT url_destino
            FROM notificaciones
            WHERE id_notificacion = :id_notificacion
                AND id_usuario = :id_usuario
            LIMIT 1
        ";

        $consultaBuscar = $this->conexion->prepare(
            $sqlBuscar
        );

        $consultaBuscar->execute([
            'id_notificacion' => $idNotificacion,
            'id_usuario' => $idUsuario
        ]);

        $url = $consultaBuscar->fetchColumn();

        if ($url === false) {
            return null;
        }

        $sqlActualizar = "
            UPDATE notificaciones
            SET
                leida = 1,
                fecha_lectura = COALESCE(
                    fecha_lectura,
                    NOW()
                )
            WHERE id_notificacion = :id_notificacion
                AND id_usuario = :id_usuario
        ";

        $consultaActualizar = $this->conexion->prepare(
            $sqlActualizar
        );

        $consultaActualizar->execute([
            'id_notificacion' => $idNotificacion,
            'id_usuario' => $idUsuario
        ]);

        return $url ?: null;
    }

    /**
     * Marca todas las notificaciones del usuario como leídas.
     */
    public function marcarTodasLeidas(
        int $idUsuario
    ): bool {
        $sql = "
            UPDATE notificaciones
            SET
                leida = 1,
                fecha_lectura = COALESCE(
                    fecha_lectura,
                    NOW()
                )
            WHERE id_usuario = :id_usuario
                AND leida = 0
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_usuario' => $idUsuario
        ]);
    }
}
