<?php

require_once __DIR__ . '/DocumentoMgModel.php';
require_once __DIR__ . '/NotificacionModel.php';

class AsignacionTutorModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los tutores activos y calcula su carga actual.
     */
    public function listarTutoresDisponibles(): array
    {
        $sql = "
            SELECT
                t.id_tutor,
                t.especialidad,
                u.nombre,
                u.apellido,
                u.correo,
                COUNT(a.id_asignacion) AS carga_actual
            FROM tutores AS t

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            LEFT JOIN asignaciones_tutor AS a
                ON a.id_tutor = t.id_tutor
                AND a.estado = 'vigente'

            WHERE u.estado = 'activo'

            GROUP BY
                t.id_tutor,
                t.especialidad,
                u.nombre,
                u.apellido,
                u.correo

            ORDER BY
                carga_actual ASC,
                u.nombre ASC,
                u.apellido ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Lista los expedientes aceptados por el tutor autenticado.
     */
    public function listarTutorados(
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                a.id_asignacion,
                a.id_expediente,
                a.fecha_asignacion,
                a.referencia_decanatura,
                a.disponibilidad_consultada,

                c.id_carta,
                c.fecha_respuesta AS fecha_aceptacion,

                x.etapa_actual,
                x.estado AS estado_expediente,
                x.titulo_trabajo,
                x.fecha_inicio,

                e.registro_universitario,
                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,
                ue.correo AS correo_estudiante,

                m.nombre AS nombre_modalidad,
                ch.nombre AS nombre_cohorte

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            INNER JOIN cartas_designacion_mg AS c
                ON c.id_asignacion = a.id_asignacion
                AND c.version_carta = (
                    SELECT MAX(c2.version_carta)
                    FROM cartas_designacion_mg AS c2
                    WHERE c2.id_asignacion = a.id_asignacion
                )

            WHERE t.id_usuario = :id_usuario
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
                AND c.estado = 'aceptada'
                AND x.estado = 'activo'

            ORDER BY
                ue.nombre ASC,
                ue.apellido ASC
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
     * Comprueba que un expediente pertenezca al tutor autenticado.
     *
     * Esta verificación se reutilizará en reuniones e informes para
     * impedir que un tutor modifique expedientes ajenos.
     */
    public function buscarTutorado(
        int $idExpediente,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                a.id_asignacion,
                a.id_expediente,
                a.id_tutor,

                x.etapa_actual,
                x.estado AS estado_expediente,
                x.titulo_trabajo,
                x.observaciones,

                e.id_estudiante,
                e.registro_universitario,

                ue.nombre AS nombre_estudiante,
                ue.apellido AS apellido_estudiante,
                ue.correo AS correo_estudiante,

                m.nombre AS nombre_modalidad,
                ch.nombre AS nombre_cohorte

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS ue
                ON ue.id_usuario = e.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            INNER JOIN cartas_designacion_mg AS c
                ON c.id_asignacion = a.id_asignacion
                AND c.version_carta = (
                    SELECT MAX(c2.version_carta)
                    FROM cartas_designacion_mg AS c2
                    WHERE c2.id_asignacion = a.id_asignacion
                )

            WHERE a.id_expediente = :id_expediente
                AND t.id_usuario = :id_usuario
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
                AND c.estado = 'aceptada'
                AND x.estado = 'activo'

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente,
            'id_usuario' => $idUsuario
        ]);

        $tutorado = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $tutorado ?: null;
    }

    /**
     * Obtiene la asignación vigente de un expediente.
     */
    public function buscarVigentePorExpediente(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                a.id_asignacion,
                a.id_expediente,
                a.id_tutor,
                a.fecha_asignacion,
                a.estado,
                a.registrado_por,
                a.referencia_decanatura,
                a.disponibilidad_consultada,

                t.especialidad,

                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,

                c.id_carta,
                c.numero_carta,
                c.version_carta,
                c.estado AS estado_carta,
                c.fecha_generacion,
                c.fecha_respuesta

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            LEFT JOIN cartas_designacion_mg AS c
                ON c.id_asignacion = a.id_asignacion
                AND c.version_carta = (
                    SELECT MAX(c2.version_carta)
                    FROM cartas_designacion_mg AS c2
                    WHERE c2.id_asignacion = a.id_asignacion
                )

            WHERE a.id_expediente = :id_expediente
                AND a.estado = 'vigente'

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente
        ]);

        $asignacion = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $asignacion ?: null;
    }

    /**
     * Recupera todo el historial de tutores de un expediente.
     */
    public function listarPorExpediente(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                a.id_asignacion,
                a.id_tutor,
                a.fecha_asignacion,
                a.fecha_fin,
                a.estado,
                a.motivo_fin,
                a.referencia_decanatura,
                a.fecha_nota_renuncia,

                t.especialidad,

                u.nombre,
                u.apellido,
                u.correo,

                c.numero_carta,
                c.estado AS estado_carta,
                c.fecha_generacion,
                c.fecha_respuesta

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            LEFT JOIN cartas_designacion_mg AS c
                ON c.id_asignacion = a.id_asignacion
                AND c.version_carta = (
                    SELECT MAX(c2.version_carta)
                    FROM cartas_designacion_mg AS c2
                    WHERE c2.id_asignacion = a.id_asignacion
                )

            WHERE a.id_expediente = :id_expediente

            ORDER BY a.fecha_asignacion DESC
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
     * Asigna un tutor, genera la carta inicial y abre MG1.
     *
     * Todas las operaciones se ejecutan juntas. Si alguna falla,
     * la base de datos vuelve al estado anterior.
     */
    public function asignar(
        int $idExpediente,
        int $idTutor,
        int $idUsuario,
        ?string $responsabilidades,
        string $referenciaDecanatura,
        bool $disponibilidadConsultada
    ): int {
        if (!$disponibilidadConsultada || trim($referenciaDecanatura) === ''
            || mb_strlen($referenciaDecanatura) > 100) {
            throw new InvalidArgumentException('Confirma la disponibilidad e indica la referencia de Decanatura.');
        }
        try {
            $this->conexion->beginTransaction();

            // Bloqueamos el expediente mientras se realiza la asignación.
            $sqlExpediente = "
                SELECT
                    x.etapa_actual,
                    x.estado,
                    m.requiere_tutor
                FROM expedientes_mg AS x

                INNER JOIN modalidades_grado AS m
                    ON m.id_modalidad = x.id_modalidad

                WHERE x.id_expediente = :id_expediente
                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaExpediente = $this->conexion->prepare(
                $sqlExpediente
            );

            $sentenciaExpediente->execute([
                'id_expediente' => $idExpediente
            ]);

            $expediente = $sentenciaExpediente->fetch(
                PDO::FETCH_ASSOC
            );

            if (!$expediente) {
                throw new RuntimeException(
                    'El expediente solicitado no existe.'
                );
            }

            if ($expediente['estado'] !== 'activo') {
                throw new RuntimeException(
                    'El expediente ya no se encuentra activo.'
                );
            }

            if ((int) $expediente['requiere_tutor'] !== 1) {
                throw new RuntimeException(
                    'Esta modalidad no requiere un tutor.'
                );
            }

            if ($expediente['etapa_actual'] !== 'previa') {
                throw new RuntimeException(
                    'El tutor inicial solamente puede asignarse en la etapa previa.'
                );
            }

            // Comprobamos que el tutor exista y tenga una cuenta activa.
            $sqlTutor = "
                SELECT t.id_tutor
                FROM tutores AS t

                INNER JOIN usuarios AS u
                    ON u.id_usuario = t.id_usuario

                WHERE t.id_tutor = :id_tutor
                    AND u.estado = 'activo'

                LIMIT 1
            ";

            $sentenciaTutor = $this->conexion->prepare(
                $sqlTutor
            );

            $sentenciaTutor->execute([
                'id_tutor' => $idTutor
            ]);

            if (!$sentenciaTutor->fetchColumn()) {
                throw new RuntimeException(
                    'El tutor seleccionado no está disponible.'
                );
            }

            // Revisamos nuevamente dentro de la transacción.
            $sqlAsignacionActual = "
                SELECT id_asignacion
                FROM asignaciones_tutor
                WHERE id_expediente = :id_expediente
                    AND estado = 'vigente'
                LIMIT 1
                FOR UPDATE
            ";

            $sentenciaAsignacionActual = $this->conexion->prepare(
                $sqlAsignacionActual
            );

            $sentenciaAsignacionActual->execute([
                'id_expediente' => $idExpediente
            ]);

            if ($sentenciaAsignacionActual->fetchColumn()) {
                throw new RuntimeException(
                    'El expediente ya tiene un tutor asignado.'
                );
            }

            // Registramos la nueva asignación.
            $sqlAsignacion = "
                INSERT INTO asignaciones_tutor (
                    id_expediente,
                    id_tutor,
                    registrado_por,
                    referencia_decanatura,
                    disponibilidad_consultada,
                    asignacion_vigente
                )
                VALUES (
                    :id_expediente,
                    :id_tutor,
                    :registrado_por,
                    :referencia_decanatura,
                    1,
                    1
                )
            ";

            $sentenciaAsignacion = $this->conexion->prepare(
                $sqlAsignacion
            );

            $sentenciaAsignacion->execute([
                'id_expediente' => $idExpediente,
                'id_tutor' => $idTutor,
                'registrado_por' => $idUsuario,
                'referencia_decanatura' => $referenciaDecanatura
            ]);

            $idAsignacion = (int) $this->conexion
                ->lastInsertId();

            // Generamos el registro inicial de la carta.
            $sqlCarta = "
                INSERT INTO cartas_designacion_mg (
                    id_asignacion,
                    version_carta,
                    estado,
                    responsabilidades,
                    creado_por
                )
                VALUES (
                    :id_asignacion,
                    1,
                    'pendiente',
                    :responsabilidades,
                    :creado_por
                )
            ";

            $sentenciaCarta = $this->conexion->prepare(
                $sqlCarta
            );

            $sentenciaCarta->execute([
                'id_asignacion' => $idAsignacion,
                'responsabilidades' => $responsabilidades,
                'creado_por' => $idUsuario
            ]);

            $idCarta = (int) $this->conexion->lastInsertId();
            $idDocumento = (new DocumentoMgModel($this->conexion))
                ->generarCartaParaAsignacion($idCarta, $idUsuario);

            $this->notificarEstudiante($idExpediente, $idDocumento);

            // Cerramos la etapa previa.
            $sqlCerrarEtapa = "
                UPDATE expediente_etapas
                SET
                    fecha_fin = CURDATE(),
                    resultado = 'completada',
                    motivo_cierre = 'Tutor asignado al expediente.'
                WHERE id_expediente = :id_expediente
                    AND etapa = 'previa'
                    AND fecha_fin IS NULL
            ";

            $sentenciaCerrarEtapa = $this->conexion->prepare(
                $sqlCerrarEtapa
            );

            $sentenciaCerrarEtapa->execute([
                'id_expediente' => $idExpediente
            ]);

            if ($sentenciaCerrarEtapa->rowCount() !== 1) {
                throw new RuntimeException(
                    'No se encontró una etapa previa abierta.'
                );
            }

            // Abrimos la etapa MG1 conservando todo el historial.
            $sqlNuevaEtapa = "
                INSERT INTO expediente_etapas (
                    id_expediente,
                    etapa,
                    fecha_inicio,
                    registrado_por
                )
                VALUES (
                    :id_expediente,
                    'mg1',
                    CURDATE(),
                    :registrado_por
                )
            ";

            $sentenciaNuevaEtapa = $this->conexion->prepare(
                $sqlNuevaEtapa
            );

            $sentenciaNuevaEtapa->execute([
                'id_expediente' => $idExpediente,
                'registrado_por' => $idUsuario
            ]);

            // Actualizamos la etapa visible del expediente.
            $sqlActualizarExpediente = "
                UPDATE expedientes_mg
                SET etapa_actual = 'mg1'
                WHERE id_expediente = :id_expediente
            ";

            $sentenciaActualizarExpediente = $this->conexion->prepare(
                $sqlActualizarExpediente
            );

            $sentenciaActualizarExpediente->execute([
                'id_expediente' => $idExpediente
            ]);

            (new BitacoraModel($this->conexion))->registrar(
                $idUsuario, 'tutor_asignado', 'asignaciones_tutor', $idAsignacion,
                null, ['id_expediente' => $idExpediente, 'id_tutor' => $idTutor]
            );

            $this->conexion->commit();

            return $idAsignacion;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

    /** Reemplaza una asignación conservando el expediente y su etapa. */
    public function cambiarTutor(
        int $idExpediente,
        int $idTutor,
        int $idUsuario,
        string $motivo,
        string $fechaNota,
        string $referenciaDecanatura,
        bool $disponibilidadConsultada,
        ?string $responsabilidades
    ): int {
        $fechaValida = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaNota);
        if (mb_strlen($motivo) < 5 || mb_strlen($motivo) > 255
            || !$disponibilidadConsultada || trim($referenciaDecanatura) === ''
            || mb_strlen($referenciaDecanatura) > 100
            || !$fechaValida || $fechaValida->format('Y-m-d') !== $fechaNota
            || $fechaNota > date('Y-m-d')) {
            throw new InvalidArgumentException('Revisa el motivo, la fecha de nota, la referencia y la disponibilidad.');
        }

        try {
            $this->conexion->beginTransaction();
            $consulta = $this->conexion->prepare("
                SELECT x.estado, x.etapa_actual, m.requiere_tutor,
                    a.id_asignacion, a.id_tutor
                FROM expedientes_mg AS x
                INNER JOIN modalidades_grado AS m ON m.id_modalidad = x.id_modalidad
                INNER JOIN asignaciones_tutor AS a ON a.id_expediente = x.id_expediente
                    AND a.estado = 'vigente' AND a.asignacion_vigente = 1
                WHERE x.id_expediente = :id_expediente LIMIT 1 FOR UPDATE
            ");
            $consulta->execute(['id_expediente' => $idExpediente]);
            $actual = $consulta->fetch(PDO::FETCH_ASSOC);
            if (!$actual || $actual['estado'] !== 'activo'
                || (int) $actual['requiere_tutor'] !== 1
                || $actual['etapa_actual'] === 'previa'
                || (int) $actual['id_tutor'] === $idTutor) {
                throw new RuntimeException('No se puede cambiar este tutor en el estado actual.');
            }

            $tutor = $this->conexion->prepare("
                SELECT t.id_tutor FROM tutores AS t
                INNER JOIN usuarios AS u ON u.id_usuario = t.id_usuario
                WHERE t.id_tutor = :id_tutor AND u.estado = 'activo' LIMIT 1
            ");
            $tutor->execute(['id_tutor' => $idTutor]);
            if (!$tutor->fetchColumn()) {
                throw new RuntimeException('El nuevo tutor no está activo.');
            }

            $cerrar = $this->conexion->prepare("
                UPDATE asignaciones_tutor
                SET estado = 'reemplazada', fecha_fin = NOW(),
                    motivo_fin = :motivo, fecha_nota_renuncia = :fecha,
                    asignacion_vigente = NULL
                WHERE id_asignacion = :id AND estado = 'vigente'
            ");
            $cerrar->execute([
                'motivo' => $motivo, 'fecha' => $fechaNota,
                'id' => (int) $actual['id_asignacion']
            ]);
            if ($cerrar->rowCount() !== 1) {
                throw new RuntimeException('La asignación cambió; vuelve a cargar la ficha.');
            }

            $anular = $this->conexion->prepare("
                UPDATE cartas_designacion_mg
                SET estado = 'anulada'
                WHERE id_asignacion = :id AND estado = 'pendiente'
            ");
            $anular->execute(['id' => (int) $actual['id_asignacion']]);

            $nueva = $this->conexion->prepare("
                INSERT INTO asignaciones_tutor (
                    id_expediente, id_tutor, registrado_por,
                    referencia_decanatura, disponibilidad_consultada,
                    asignacion_vigente
                ) VALUES (:expediente, :tutor, :usuario, :referencia, 1, 1)
            ");
            $nueva->execute([
                'expediente' => $idExpediente, 'tutor' => $idTutor,
                'usuario' => $idUsuario, 'referencia' => $referenciaDecanatura
            ]);
            $idAsignacion = (int) $this->conexion->lastInsertId();
            $carta = $this->conexion->prepare("
                INSERT INTO cartas_designacion_mg (
                    id_asignacion, version_carta, estado, responsabilidades, creado_por
                ) VALUES (:asignacion, 1, 'pendiente', :responsabilidades, :usuario)
            ");
            $carta->execute([
                'asignacion' => $idAsignacion,
                'responsabilidades' => $responsabilidades,
                'usuario' => $idUsuario
            ]);
            $idDocumento = (new DocumentoMgModel($this->conexion))->generarCartaParaAsignacion(
                (int) $this->conexion->lastInsertId(), $idUsuario
            );
            $this->notificarEstudiante($idExpediente, $idDocumento);
            (new BitacoraModel($this->conexion))->registrar(
                $idUsuario, 'tutor_reemplazado', 'asignaciones_tutor', $idAsignacion,
                ['id_asignacion' => (int) $actual['id_asignacion'],
                    'id_tutor' => (int) $actual['id_tutor']],
                ['id_expediente' => $idExpediente, 'id_tutor' => $idTutor]
            );
            $this->conexion->commit();
            return $idAsignacion;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    private function notificarEstudiante(int $idExpediente, int $idDocumento): void
    {
        $consulta = $this->conexion->prepare("
            SELECT e.id_usuario FROM expedientes_mg AS x
            INNER JOIN estudiantes AS e ON e.id_estudiante = x.id_estudiante
            WHERE x.id_expediente = :id LIMIT 1
        ");
        $consulta->execute(['id' => $idExpediente]);
        $id = (int) $consulta->fetchColumn();
        if ($id > 0) {
            (new NotificacionModel($this->conexion))->crearParaUsuario(
                $id,
                'designacion_tutor',
                'Designación de tutor',
                'Se emitió una nueva carta de designación de tutor para tu expediente.',
                'controllers/documento_ver.php?id=' . $idDocumento
            );
        }
    }
}
