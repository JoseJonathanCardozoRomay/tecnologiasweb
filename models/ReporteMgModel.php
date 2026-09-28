<?php

class ReporteMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene la información principal de un expediente.
     */
    public function buscarExpediente(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                e.*,

                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,

                es.registro_universitario,
                es.semestre,

                ca.nombre_carrera,
                m.nombre AS modalidad,
                c.nombre AS cohorte,

                CONCAT(
                    ut.nombre,
                    ' ',
                    ut.apellido
                ) AS tutor_actual

            FROM expedientes_mg AS e

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN carreras AS ca
                ON ca.id_carrera = es.id_carrera

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            LEFT JOIN asignaciones_tutor AS a
                ON a.id_expediente = e.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            LEFT JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            LEFT JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

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
     * Historial de tutores del expediente.
     */
    public function listarTutores(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                a.fecha_asignacion,
                a.fecha_fin,
                a.estado,
                a.motivo_fin,

                u.nombre,
                u.apellido

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE a.id_expediente = :id_expediente

            ORDER BY a.fecha_asignacion ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Tribunales actuales e históricos.
     */
    public function listarTribunales(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                td.etapa,
                td.orden,
                td.fecha_asignacion,
                td.fecha_fin,
                td.estado,

                u.nombre,
                u.apellido

            FROM tribunales_defensa AS td

            INNER JOIN tutores AS t
                ON t.id_tutor = td.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE td.id_expediente = :id_expediente

            ORDER BY
                td.etapa ASC,
                td.orden ASC,
                td.fecha_asignacion ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Defensas y calificaciones.
     */
    public function listarDefensas(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                d.id_defensa,
                d.etapa,
                d.fecha,
                d.hora_inicio,
                d.hora_fin,
                d.ambiente,
                d.estado,

                c.nota,
                c.publicada

            FROM defensas_mg AS d

            LEFT JOIN calificaciones_mg AS c
                ON c.id_defensa = d.id_defensa

            WHERE d.id_expediente = :id_expediente

            ORDER BY d.fecha ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Resumen del seguimiento académico.
     */
    public function obtenerSeguimiento(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                (
                    SELECT COUNT(*)
                    FROM reuniones_mg AS r
                    INNER JOIN asignaciones_tutor AS a
                        ON a.id_asignacion = r.id_asignacion
                    WHERE a.id_expediente = :expediente_reuniones
                ) AS total_reuniones,

                (
                    SELECT COUNT(*)
                    FROM reuniones_mg AS r
                    INNER JOIN asignaciones_tutor AS a
                        ON a.id_asignacion = r.id_asignacion
                    WHERE a.id_expediente = :expediente_validadas
                        AND r.estado_validacion = 'validada'
                ) AS reuniones_validadas,

                (
                    SELECT COUNT(*)
                    FROM informes_avance_mg AS i
                    WHERE i.id_expediente = :expediente_informes
                ) AS total_informes,

                (
                    SELECT MAX(porcentaje_avance)
                    FROM informes_avance_mg AS i
                    WHERE i.id_expediente = :expediente_avance
                ) AS ultimo_avance,

                (
                    SELECT COUNT(*)
                    FROM documentos_generados AS d
                    WHERE d.id_expediente = :expediente_documentos
                ) AS total_documentos
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'expediente_reuniones' => $idExpediente,
            'expediente_validadas' => $idExpediente,
            'expediente_informes' => $idExpediente,
            'expediente_avance' => $idExpediente,
            'expediente_documentos' => $idExpediente
        ]);

        return $consulta->fetch(
            PDO::FETCH_ASSOC
        ) ?: [];
    }

    /**
     * Reporte general con filtros opcionales.
     */
    public function listarGeneral(
        ?int $idCohorte,
        ?int $idModalidad,
        string $etapa,
        string $estado
    ): array {
        $sql = "
            SELECT
                e.id_expediente,
                e.etapa_actual,
                e.estado,
                e.fecha_inicio,
                e.titulo_trabajo,

                u.nombre,
                u.apellido,

                m.nombre AS modalidad,
                c.nombre AS cohorte,

                CONCAT(
                    ut.nombre,
                    ' ',
                    ut.apellido
                ) AS tutor_actual,

                (
                    SELECT i.porcentaje_avance
                    FROM informes_avance_mg AS i
                    WHERE i.id_expediente = e.id_expediente
                    ORDER BY
                        i.fecha_informe DESC,
                        i.id_informe DESC
                    LIMIT 1
                ) AS ultimo_avance

            FROM expedientes_mg AS e

            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = e.id_modalidad

            INNER JOIN cohortes_mg AS c
                ON c.id_cohorte = e.id_cohorte

            LEFT JOIN asignaciones_tutor AS a
                ON a.id_expediente = e.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            LEFT JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            LEFT JOIN usuarios AS ut
                ON ut.id_usuario = t.id_usuario

            WHERE 1 = 1
        ";

        $parametros = [];

        if ($idCohorte !== null) {
            $sql .= "
                AND e.id_cohorte = :id_cohorte
            ";

            $parametros['id_cohorte'] = $idCohorte;
        }

        if ($idModalidad !== null) {
            $sql .= "
                AND e.id_modalidad = :id_modalidad
            ";

            $parametros['id_modalidad'] = $idModalidad;
        }

        if ($etapa !== '') {
            $sql .= "
                AND e.etapa_actual = :etapa
            ";

            $parametros['etapa'] = $etapa;
        }

        if ($estado !== '') {
            $sql .= "
                AND e.estado = :estado
            ";

            $parametros['estado'] = $estado;
        }

        $sql .= "
            ORDER BY
                c.fecha_inicio DESC,
                u.nombre ASC,
                u.apellido ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function listarCohortes(): array
    {
        $consulta = $this->conexion->prepare(
            "
                SELECT id_cohorte, nombre
                FROM cohortes_mg
                ORDER BY fecha_inicio DESC
            "
        );

        $consulta->execute();

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    public function listarModalidades(): array
    {
        $consulta = $this->conexion->prepare(
            "
                SELECT id_modalidad, nombre
                FROM modalidades_grado
                ORDER BY nombre ASC
            "
        );

        $consulta->execute();

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}