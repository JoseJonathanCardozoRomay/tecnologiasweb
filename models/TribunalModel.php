<?php

class TribunalModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los expedientes que pueden recibir tribunales.
     */
    public function listarExpedientes(
        string $busqueda = ''
    ): array {
        $sql = "
            SELECT
                e.id_expediente,
                e.etapa_actual,
                e.estado,
                e.titulo_trabajo,

                u.nombre,
                u.apellido,
                u.usuario,

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
                AND (
                    u.nombre LIKE :buscar_nombre
                    OR u.apellido LIKE :buscar_apellido
                    OR u.usuario LIKE :buscar_usuario
                    OR COALESCE(
                        e.titulo_trabajo,
                        ''
                    ) LIKE :buscar_titulo
                )

            GROUP BY
                e.id_expediente,
                e.etapa_actual,
                e.estado,
                e.titulo_trabajo,
                u.nombre,
                u.apellido,
                u.usuario,
                m.nombre,
                c.nombre

            ORDER BY
                u.nombre ASC,
                u.apellido ASC
        ";

        $patron = '%' . trim($busqueda) . '%';

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'buscar_nombre' => $patron,
            'buscar_apellido' => $patron,
            'buscar_usuario' => $patron,
            'buscar_titulo' => $patron
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene los datos principales de un expediente.
     */
    public function buscarExpediente(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                e.id_expediente,
                e.etapa_actual,
                e.estado,
                e.titulo_trabajo,

                u.nombre,
                u.apellido,
                u.usuario,

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
     * Lista los docentes activos disponibles para ser tribunales.
     */
    public function listarDocentesActivos(): array
    {
        $sql = "
            SELECT
                t.id_tutor,
                t.especialidad,
                u.nombre,
                u.apellido,
                u.correo

            FROM tutores AS t

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE u.estado = 'activo'

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
     * Lista los tribunales vigentes e históricos de un expediente.
     */
    public function listarPorExpediente(
        int $idExpediente
    ): array {
        $sql = "
            SELECT
                td.id_tribunal,
                td.id_expediente,
                td.etapa,
                td.id_tutor,
                td.orden,
                td.fecha_asignacion,
                td.fecha_fin,
                td.estado,
                td.motivo_cambio,

                u.nombre,
                u.apellido,
                u.correo,
                t.especialidad

            FROM tribunales_defensa AS td

            INNER JOIN tutores AS t
                ON t.id_tutor = td.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE td.id_expediente = :id_expediente

            ORDER BY
                td.etapa ASC,
                td.orden ASC,
                td.fecha_asignacion DESC
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
     * Obtiene una asignación de tribunal mediante su identificador.
     */
    public function buscarPorId(
        int $idTribunal
    ): ?array {
        $sql = "
            SELECT
                td.id_tribunal,
                td.id_expediente,
                td.etapa,
                td.id_tutor,
                td.orden,
                td.fecha_asignacion,
                td.estado,
                td.asignacion_vigente,

                u.nombre,
                u.apellido

            FROM tribunales_defensa AS td

            INNER JOIN tutores AS t
                ON t.id_tutor = td.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE td.id_tribunal = :id_tribunal
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_tribunal' => $idTribunal
        ]);

        $tribunal = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $tribunal ?: null;
    }

    /**
     * Obtiene el tutor principal vigente del expediente.
     *
     * Se utiliza para mostrar una advertencia cuando se pretende
     * elegir al mismo docente como tribunal. Por ahora no se bloquea,
     * porque esta regla todavía debe confirmarse institucionalmente.
     */
    public function buscarTutorPrincipal(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                a.id_tutor,
                u.nombre,
                u.apellido

            FROM asignaciones_tutor AS a

            INNER JOIN tutores AS t
                ON t.id_tutor = a.id_tutor

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE a.id_expediente = :id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1

            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente
        ]);

        $tutor = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $tutor ?: null;
    }

    /**
     * Comprueba que el docente exista y mantenga su cuenta activa.
     */
    public function docenteDisponible(
        int $idTutor
    ): bool {
        $sql = "
            SELECT COUNT(*)

            FROM tutores AS t

            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario

            WHERE t.id_tutor = :id_tutor
                AND u.estado = 'activo'
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_tutor' => $idTutor
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Comprueba si el docente ya es tribunal vigente en la etapa.
     */
    public function docenteYaAsignado(
        int $idExpediente,
        string $etapa,
        int $idTutor
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM tribunales_defensa
            WHERE id_expediente = :id_expediente
                AND etapa = :etapa
                AND id_tutor = :id_tutor
                AND estado = 'vigente'
                AND asignacion_vigente = 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa,
            'id_tutor' => $idTutor
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Comprueba si una posición ya está ocupada.
     */
    public function ordenOcupado(
        int $idExpediente,
        string $etapa,
        int $orden
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM tribunales_defensa
            WHERE id_expediente = :id_expediente
                AND etapa = :etapa
                AND orden = :orden
                AND estado = 'vigente'
                AND asignacion_vigente = 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa,
            'orden' => $orden
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Registra un tribunal para la etapa actual del expediente.
     */
    public function asignar(
        int $idExpediente,
        string $etapa,
        int $idTutor,
        int $orden,
        int $registradoPor
    ): bool {
        $sql = "
            INSERT INTO tribunales_defensa (
                id_expediente,
                etapa,
                id_tutor,
                orden,
                registrado_por
            )
            VALUES (
                :id_expediente,
                :etapa,
                :id_tutor,
                :orden,
                :registrado_por
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_expediente' => $idExpediente,
            'etapa' => $etapa,
            'id_tutor' => $idTutor,
            'orden' => $orden,
            'registrado_por' => $registradoPor
        ]);
    }

    /**
     * Reemplaza un tribunal conservando el registro anterior.
     */
    public function reemplazar(
        int $idTribunal,
        int $idNuevoTutor,
        string $motivoCambio,
        int $registradoPor
    ): bool {
        try {
            $this->conexion->beginTransaction();

            $sqlTribunal = "
                SELECT
                    id_expediente,
                    etapa,
                    orden,
                    estado,
                    asignacion_vigente

                FROM tribunales_defensa

                WHERE id_tribunal = :id_tribunal
                LIMIT 1
                FOR UPDATE
            ";

            $consultaTribunal = $this->conexion->prepare(
                $sqlTribunal
            );

            $consultaTribunal->execute([
                'id_tribunal' => $idTribunal
            ]);

            $tribunalActual = $consultaTribunal->fetch(
                PDO::FETCH_ASSOC
            );

            if (
                !$tribunalActual
                || $tribunalActual['estado'] !== 'vigente'
                || (int) $tribunalActual['asignacion_vigente'] !== 1
            ) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlCerrar = "
                UPDATE tribunales_defensa
                SET
                    estado = 'reemplazado',
                    fecha_fin = NOW(),
                    motivo_cambio = :motivo_cambio,
                    asignacion_vigente = NULL
                WHERE id_tribunal = :id_tribunal
            ";

            $consultaCerrar = $this->conexion->prepare(
                $sqlCerrar
            );

            $consultaCerrar->execute([
                'motivo_cambio' => trim($motivoCambio),
                'id_tribunal' => $idTribunal
            ]);

            $sqlNuevo = "
                INSERT INTO tribunales_defensa (
                    id_expediente,
                    etapa,
                    id_tutor,
                    orden,
                    registrado_por
                )
                VALUES (
                    :id_expediente,
                    :etapa,
                    :id_tutor,
                    :orden,
                    :registrado_por
                )
            ";

            $consultaNueva = $this->conexion->prepare(
                $sqlNuevo
            );

            $consultaNueva->execute([
                'id_expediente' => $tribunalActual['id_expediente'],
                'etapa' => $tribunalActual['etapa'],
                'id_tutor' => $idNuevoTutor,
                'orden' => $tribunalActual['orden'],
                'registrado_por' => $registradoPor
            ]);

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