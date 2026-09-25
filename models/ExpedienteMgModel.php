<?php

class ExpedienteMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Construye los filtros compartidos por el listado y el contador.
     */
    private function construirFiltros(
        string $busqueda,
        ?int $idCohorte,
        ?int $idModalidad,
        ?string $etapa,
        ?string $estado
    ): array {
        $sql = "
            WHERE (
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR e.registro_universitario LIKE :buscar_registro
                OR COALESCE(
                    x.titulo_trabajo,
                    ''
                ) LIKE :buscar_titulo
            )
        ";

        $patronBusqueda = '%'
            . trim($busqueda)
            . '%';

        $parametros = [
            'buscar_nombre' => $patronBusqueda,
            'buscar_apellido' => $patronBusqueda,
            'buscar_registro' => $patronBusqueda,
            'buscar_titulo' => $patronBusqueda
        ];

        if ($idCohorte !== null) {
            $sql .= "
                AND x.id_cohorte = :id_cohorte
            ";

            $parametros['id_cohorte'] = $idCohorte;
        }

        if ($idModalidad !== null) {
            $sql .= "
                AND x.id_modalidad = :id_modalidad
            ";

            $parametros['id_modalidad'] = $idModalidad;
        }

        if ($etapa !== null) {
            $sql .= "
                AND x.etapa_actual = :etapa
            ";

            $parametros['etapa'] = $etapa;
        }

        if ($estado !== null) {
            $sql .= "
                AND x.estado = :estado
            ";

            $parametros['estado'] = $estado;
        }

        return [
            'sql' => $sql,
            'parametros' => $parametros
        ];
    }

    /**
     * Lista expedientes con filtros y paginación.
     */
    public function listar(
        string $busqueda = '',
        ?int $idCohorte = null,
        ?int $idModalidad = null,
        ?string $etapa = null,
        ?string $estado = null,
        int $limite = 20,
        int $desplazamiento = 0
    ): array {
        $filtros = $this->construirFiltros(
            $busqueda,
            $idCohorte,
            $idModalidad,
            $etapa,
            $estado
        );

        $sql = "
            SELECT
                x.id_expediente,
                x.id_estudiante,
                x.id_modalidad,
                x.id_cohorte,
                x.etapa_actual,
                x.estado,
                x.titulo_trabajo,
                x.fecha_inicio,
                x.fecha_cierre,
                x.observaciones,
                x.fecha_registro,

                e.registro_universitario,
                e.semestre,

                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,

                c.nombre_carrera,

                m.codigo AS codigo_modalidad,
                m.nombre AS nombre_modalidad,
                m.requiere_tutor,

                co.codigo AS codigo_cohorte,
                co.nombre AS nombre_cohorte

            FROM expedientes_mg AS x

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_usuario

            INNER JOIN carreras AS c
                ON c.id_carrera = e.id_carrera

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS co
                ON co.id_cohorte = x.id_cohorte
        ";

        $sql .= $filtros['sql'];

        $sql .= "
            ORDER BY
                x.fecha_inicio DESC,
                u.nombre ASC,
                u.apellido ASC
            LIMIT :limite
            OFFSET :desplazamiento
        ";

        $sentencia = $this->conexion->prepare($sql);

        foreach (
            $filtros['parametros']
            as $nombreParametro => $valorParametro
        ) {
            $sentencia->bindValue(
                ':' . $nombreParametro,
                $valorParametro,
                PDO::PARAM_STR
            );
        }

        $sentencia->bindValue(
            ':limite',
            max(1, $limite),
            PDO::PARAM_INT
        );

        $sentencia->bindValue(
            ':desplazamiento',
            max(0, $desplazamiento),
            PDO::PARAM_INT
        );

        $sentencia->execute();

        return $sentencia->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Cuenta los expedientes que cumplen los filtros.
     */
    public function contar(
        string $busqueda = '',
        ?int $idCohorte = null,
        ?int $idModalidad = null,
        ?string $etapa = null,
        ?string $estado = null
    ): int {
        $filtros = $this->construirFiltros(
            $busqueda,
            $idCohorte,
            $idModalidad,
            $etapa,
            $estado
        );

        $sql = "
            SELECT COUNT(*)

            FROM expedientes_mg AS x

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_usuario
        ";

        $sql .= $filtros['sql'];

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($filtros['parametros']);

        return (int) $sentencia->fetchColumn();
    }

    /**
     * Busca la información completa de un expediente.
     */
    public function buscarPorId(
        int $idExpediente
    ): ?array {
        $sql = "
            SELECT
                x.id_expediente,
                x.id_estudiante,
                x.id_modalidad,
                x.id_cohorte,
                x.etapa_actual,
                x.estado,
                x.titulo_trabajo,
                x.fecha_inicio,
                x.fecha_cierre,
                x.observaciones,
                x.creado_por,
                x.fecha_registro,

                e.registro_universitario,
                e.semestre,

                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,

                c.nombre_carrera,

                m.codigo AS codigo_modalidad,
                m.nombre AS nombre_modalidad,
                m.requiere_tutor,

                co.codigo AS codigo_cohorte,
                co.nombre AS nombre_cohorte

            FROM expedientes_mg AS x

            INNER JOIN estudiantes AS e
                ON e.id_estudiante = x.id_estudiante

            INNER JOIN usuarios AS u
                ON u.id_usuario = e.id_usuario

            INNER JOIN carreras AS c
                ON c.id_carrera = e.id_carrera

            INNER JOIN modalidades_grado AS m
                ON m.id_modalidad = x.id_modalidad

            INNER JOIN cohortes_mg AS co
                ON co.id_cohorte = x.id_cohorte

            WHERE x.id_expediente = :id_expediente
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_expediente' => $idExpediente
        ]);

        $expediente = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $expediente ?: null;
    }

    /**
     * Comprueba que el mismo proceso no esté registrado dos veces.
     */
    public function existeProceso(
        int $idEstudiante,
        int $idModalidad,
        int $idCohorte
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM expedientes_mg
            WHERE id_estudiante = :id_estudiante
                AND id_modalidad = :id_modalidad
                AND id_cohorte = :id_cohorte
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_estudiante' => $idEstudiante,
            'id_modalidad' => $idModalidad,
            'id_cohorte' => $idCohorte
        ]);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Crea el expediente y su primera etapa en una transacción.
     */
    public function crear(
        int $idEstudiante,
        int $idModalidad,
        int $idCohorte,
        ?string $tituloTrabajo,
        string $fechaInicio,
        ?string $observaciones,
        int $idUsuario
    ): int {
        try {
            $this->conexion->beginTransaction();

            $sqlExpediente = "
                INSERT INTO expedientes_mg (
                    id_estudiante,
                    id_modalidad,
                    id_cohorte,
                    etapa_actual,
                    estado,
                    titulo_trabajo,
                    fecha_inicio,
                    observaciones,
                    creado_por
                )
                VALUES (
                    :id_estudiante,
                    :id_modalidad,
                    :id_cohorte,
                    'previa',
                    'activo',
                    :titulo_trabajo,
                    :fecha_inicio,
                    :observaciones,
                    :creado_por
                )
            ";

            $sentenciaExpediente = $this->conexion->prepare(
                $sqlExpediente
            );

            $sentenciaExpediente->execute([
                'id_estudiante' => $idEstudiante,
                'id_modalidad' => $idModalidad,
                'id_cohorte' => $idCohorte,
                'titulo_trabajo' => $tituloTrabajo,
                'fecha_inicio' => $fechaInicio,
                'observaciones' => $observaciones,
                'creado_por' => $idUsuario
            ]);

            $idExpediente = (int) $this->conexion
                ->lastInsertId();

            $sqlEtapa = "
                INSERT INTO expediente_etapas (
                    id_expediente,
                    etapa,
                    fecha_inicio,
                    registrado_por
                )
                VALUES (
                    :id_expediente,
                    'previa',
                    :fecha_inicio,
                    :registrado_por
                )
            ";

            $sentenciaEtapa = $this->conexion->prepare(
                $sqlEtapa
            );

            $sentenciaEtapa->execute([
                'id_expediente' => $idExpediente,
                'fecha_inicio' => $fechaInicio,
                'registrado_por' => $idUsuario
            ]);

            $this->conexion->commit();

            return $idExpediente;
        } catch (Throwable $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $e;
        }
    }

        /**
     * Actualiza los datos generales de un expediente.
     */
    public function actualizarDatos(
        int $idExpediente,
        ?string $tituloTrabajo,
        ?string $observaciones
    ): bool {
        $sql = "
            UPDATE expedientes_mg
            SET
                titulo_trabajo = :titulo_trabajo,
                observaciones = :observaciones
            WHERE id_expediente = :id_expediente
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'titulo_trabajo' => $tituloTrabajo,
            'observaciones' => $observaciones,
            'id_expediente' => $idExpediente
        ]);
    }
}