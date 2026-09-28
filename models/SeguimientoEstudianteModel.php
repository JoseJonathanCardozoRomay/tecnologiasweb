<?php

class SeguimientoEstudianteModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista únicamente los expedientes del estudiante autenticado.
     */
    public function listarExpedientes(
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                x.id_expediente,
                x.etapa_actual,
                x.estado,
                x.titulo_trabajo,
                x.fecha_inicio,
                x.fecha_cierre,

                m.nombre AS nombre_modalidad,
                m.requiere_tutor,

                ch.nombre AS nombre_cohorte,
                ch.fecha_inicio AS fecha_inicio_cohorte,
                ch.fecha_fin AS fecha_fin_cohorte,

                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor,
                t.especialidad,

                cd.estado AS estado_carta

            FROM expedientes_mg AS x

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            LEFT JOIN asignaciones_tutor AS a
                ON a.id_expediente = x.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            LEFT JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            LEFT JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            LEFT JOIN cartas_designacion_mg AS cd
                ON cd.id_asignacion = a.id_asignacion
                AND cd.version_carta = (
                    SELECT MAX(cd2.version_carta)
                    FROM cartas_designacion_mg AS cd2
                    WHERE cd2.id_asignacion = a.id_asignacion
                )

            WHERE e.id_usuario = :id_usuario

            ORDER BY
                x.fecha_inicio DESC,
                x.id_expediente DESC
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
     * Obtiene un expediente comprobando que sea del estudiante.
     */
    public function buscarExpediente(
        int $idExpediente,
        int $idUsuario
    ): ?array {
        $sql = "
            SELECT
                x.*,

                m.nombre AS nombre_modalidad,
                m.descripcion AS descripcion_modalidad,
                m.requiere_tutor,

                ch.nombre AS nombre_cohorte,
                ch.fecha_inicio AS fecha_inicio_cohorte,
                ch.fecha_fin AS fecha_fin_cohorte,

                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor,
                ut.correo AS correo_tutor,
                t.especialidad,

                cd.estado AS estado_carta,
                cd.fecha_respuesta

            FROM expedientes_mg AS x

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = x.id_cohorte

            LEFT JOIN asignaciones_tutor AS a
                ON a.id_expediente = x.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            LEFT JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            LEFT JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            LEFT JOIN cartas_designacion_mg AS cd
                ON cd.id_asignacion = a.id_asignacion
                AND cd.version_carta = (
                    SELECT MAX(cd2.version_carta)
                    FROM cartas_designacion_mg AS cd2
                    WHERE cd2.id_asignacion = a.id_asignacion
                )

            WHERE x.id_expediente = :id_expediente
                AND e.id_usuario = :id_usuario

            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente,
            'id_usuario' => $idUsuario
        ]);

        $expediente = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $expediente ?: null;
    }

    /**
     * Lista las reuniones del expediente propio.
     */
    public function listarReuniones(
        int $idExpediente,
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                r.id_reunion,
                r.fecha_reunion,
                r.hora_inicio,
                r.hora_fin,
                r.modalidad,
                r.lugar_enlace,
                r.tema,
                r.acuerdos,
                r.estado,
                r.asistencia_estudiante,
                r.estado_validacion,

                ut.nombre AS nombre_tutor,
                ut.apellido AS apellido_tutor

            FROM reuniones_mg AS r

            INNER JOIN asignaciones_tutor AS a
                ON a.id_asignacion = r.id_asignacion

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = a.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            WHERE x.id_expediente = :id_expediente
                AND e.id_usuario = :id_usuario
                AND r.estado != 'cancelada'

            ORDER BY
                r.fecha_reunion DESC,
                r.hora_inicio DESC
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
     * Muestra únicamente informes aprobados por coordinación.
     */
    public function listarInformesAprobados(
        int $idExpediente,
        int $idUsuario
    ): array {
        $sql = "
            SELECT
                i.id_informe,
                i.etapa,
                i.numero_informe,
                i.fecha_informe,
                i.porcentaje_avance,
                i.resumen_avance,
                i.logros,
                i.dificultades,
                i.recomendaciones,
                i.proximas_actividades,
                i.fecha_revision

            FROM informes_avance_mg AS i

            INNER JOIN expedientes_mg AS x
                ON x.id_expediente = i.id_expediente

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            WHERE i.id_expediente = :id_expediente
                AND e.id_usuario = :id_usuario
                AND i.estado = 'aprobado'

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
     * Lista solamente el calendario de las cohortes del estudiante.
     */
    public function listarCalendario(
        int $idUsuario
    ): array {
        $sql = "
            SELECT DISTINCT
                h.id_hito,
                h.etapa,
                h.tipo,
                h.nombre,
                h.orden,
                h.fecha_limite,
                h.avance_esperado_pct,

                ch.id_cohorte,
                ch.nombre AS nombre_cohorte

            FROM calendario_mg AS h

            INNER JOIN cohortes_mg AS ch
                ON ch.id_cohorte = h.id_cohorte

            INNER JOIN expedientes_mg AS x
                ON x.id_cohorte = ch.id_cohorte

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            WHERE e.id_usuario = :id_usuario

            ORDER BY
                h.fecha_limite ASC,
                h.orden ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_usuario' => $idUsuario
        ]);

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}