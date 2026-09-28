<?php

class AlertaMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene parámetros configurables utilizados por las alertas.
     */
    private function obtenerParametros(): array
    {
        $sql = "
            SELECT clave, valor
            FROM parametros_mg
            WHERE clave IN (
                'dias_alerta_sin_reunion',
                'reuniones_min_semana_perfil',
                'dias_anticipacion_tribunal',
                'tribunales_por_defensa_mg1',
                'tribunales_por_defensa_mg2',
                'tutor_carga_recomendada'
            )
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        $parametros = [];

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $parametro
        ) {
            $parametros[$parametro['clave']]
                = (int) $parametro['valor'];
        }

        return [
            'dias_sin_reunion'
                => $parametros[
                    'dias_alerta_sin_reunion'
                ] ?? 10,
            'reuniones_semana'
                => $parametros[
                    'reuniones_min_semana_perfil'
                ] ?? 2,
            'anticipacion_tribunal'
                => $parametros[
                    'dias_anticipacion_tribunal'
                ] ?? 14,
            'tribunales_mg1'
                => $parametros[
                    'tribunales_por_defensa_mg1'
                ] ?? 2,
            'tribunales_mg2'
                => $parametros[
                    'tribunales_por_defensa_mg2'
                ] ?? 2,
            'carga_tutor'
                => $parametros[
                    'tutor_carga_recomendada'
                ] ?? 3
        ];
    }

    /**
     * Agrega una alerta al resultado.
     */
    private function agregar(
        array &$alertas,
        string $tipo,
        int $referencia,
        string $severidad,
        string $titulo,
        string $descripcion,
        string $url
    ): void {
        $alertas[] = [
            'clave' => $tipo . ':' . $referencia,
            'tipo' => $tipo,
            'id_referencia' => $referencia,
            'severidad' => $severidad,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'url' => $url
        ];
    }

    /**
     * Calcula las alertas actuales del sistema.
     */
    public function calcular(): array
    {
        $parametros = $this->obtenerParametros();
        $alertas = [];

        // A1: Expedientes MG1 sin tutor.
        $sql = "
            SELECT
                e.id_expediente,
                u.nombre,
                u.apellido
            FROM expedientes_mg AS e
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            WHERE e.estado = 'activo'
                AND e.etapa_actual = 'mg1'
                AND NOT EXISTS (
                    SELECT 1
                    FROM asignaciones_tutor AS a
                    WHERE a.id_expediente = e.id_expediente
                        AND a.estado = 'vigente'
                        AND a.asignacion_vigente = 1
                )
        ";

        foreach (
            $this->conexion->query($sql)->fetchAll(
                PDO::FETCH_ASSOC
            ) as $fila
        ) {
            $this->agregar(
                $alertas,
                'sin_tutor',
                (int) $fila['id_expediente'],
                'alta',
                'Expediente sin tutor',
                $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' se encuentra en MG1 sin tutor vigente.',
                'controllers/expedientes_ver.php?id='
                    . $fila['id_expediente']
            );
        }

        // A2: Expedientes sin reuniones recientes.
        $sql = "
            SELECT
                e.id_expediente,
                u.nombre,
                u.apellido,
                MAX(r.fecha_reunion) AS ultima_reunion
            FROM expedientes_mg AS e
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            INNER JOIN asignaciones_tutor AS a
                ON a.id_expediente = e.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
            LEFT JOIN reuniones_mg AS r
                ON r.id_asignacion = a.id_asignacion
                AND r.estado != 'cancelada'
            WHERE e.estado = 'activo'
                AND e.etapa_actual IN ('mg1', 'mg2')
            GROUP BY
                e.id_expediente,
                u.nombre,
                u.apellido
            HAVING ultima_reunion IS NULL
                OR DATEDIFF(
                    CURDATE(),
                    ultima_reunion
                ) > :dias
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'dias' => $parametros['dias_sin_reunion']
        ]);

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $fila
        ) {
            $this->agregar(
                $alertas,
                'sin_reunion',
                (int) $fila['id_expediente'],
                'media',
                'Seguimiento sin reuniones',
                $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' no tiene reuniones recientes.',
                'controllers/tutorado_ver.php?id='
                    . $fila['id_expediente']
            );
        }

        // A3: Menos reuniones semanales que las recomendadas.
        $sql = "
            SELECT
                e.id_expediente,
                u.nombre,
                u.apellido,
                COUNT(r.id_reunion) AS reuniones_semana
            FROM expedientes_mg AS e
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            INNER JOIN asignaciones_tutor AS a
                ON a.id_expediente = e.id_expediente
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
            LEFT JOIN reuniones_mg AS r
                ON r.id_asignacion = a.id_asignacion
                AND YEARWEEK(
                    r.fecha_reunion,
                    1
                ) = YEARWEEK(CURDATE(), 1)
                AND r.estado != 'cancelada'
            WHERE e.estado = 'activo'
                AND e.etapa_actual = 'mg1'
            GROUP BY
                e.id_expediente,
                u.nombre,
                u.apellido
            HAVING reuniones_semana < :minimo
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'minimo' => $parametros['reuniones_semana']
        ]);

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $fila
        ) {
            $this->agregar(
                $alertas,
                'reuniones_insuficientes',
                (int) $fila['id_expediente'],
                'media',
                'Reuniones semanales insuficientes',
                $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' registra '
                    . $fila['reuniones_semana']
                    . ' reuniones esta semana.',
                'controllers/reuniones_revision_listar.php'
            );
        }

        // A4: Informes vencidos sin presentar.
        $sql = "
            SELECT
                e.id_expediente,
                h.id_hito,
                h.nombre AS hito,
                h.fecha_limite,
                u.nombre,
                u.apellido
            FROM expedientes_mg AS e
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            INNER JOIN calendario_mg AS h
                ON h.id_cohorte = e.id_cohorte
                AND h.etapa = e.etapa_actual
                AND h.tipo = 'informe'
            WHERE e.estado = 'activo'
                AND h.fecha_limite < CURDATE()
                AND NOT EXISTS (
                    SELECT 1
                    FROM informes_avance_mg AS i
                    WHERE i.id_expediente = e.id_expediente
                        AND i.id_hito = h.id_hito
                        AND i.estado IN (
                            'presentado',
                            'aprobado',
                            'observado'
                        )
                )
        ";

        foreach (
            $this->conexion->query($sql)->fetchAll(
                PDO::FETCH_ASSOC
            ) as $fila
        ) {
            $referencia = (int) $fila['id_expediente']
                * 100000
                + (int) $fila['id_hito'];

            $this->agregar(
                $alertas,
                'informe_vencido',
                $referencia,
                'alta',
                'Informe vencido',
                $fila['hito']
                    . ' de '
                    . $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' venció el '
                    . date(
                        'd/m/Y',
                        strtotime($fila['fecha_limite'])
                    )
                    . '.',
                'controllers/informes_revision_listar.php'
            );
        }

        // A5: Avance menor al esperado.
        $sql = "
            SELECT
                i.id_informe,
                i.porcentaje_avance,
                h.avance_esperado_pct,
                u.nombre,
                u.apellido
            FROM informes_avance_mg AS i
            INNER JOIN calendario_mg AS h
                ON h.id_hito = i.id_hito
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = i.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            WHERE h.avance_esperado_pct IS NOT NULL
                AND i.porcentaje_avance
                    < h.avance_esperado_pct
        ";

        foreach (
            $this->conexion->query($sql)->fetchAll(
                PDO::FETCH_ASSOC
            ) as $fila
        ) {
            $this->agregar(
                $alertas,
                'avance_bajo',
                (int) $fila['id_informe'],
                'media',
                'Avance inferior al esperado',
                $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' registra '
                    . $fila['porcentaje_avance']
                    . '% frente a '
                    . $fila['avance_esperado_pct']
                    . '% esperado.',
                'controllers/informe_revision_ver.php?id='
                    . $fila['id_informe']
            );
        }

        // A7: Defensa próxima sin suficientes tribunales.
        $sql = "
            SELECT
                d.id_defensa,
                d.id_expediente,
                d.etapa,
                d.fecha,
                u.nombre,
                u.apellido,
                COUNT(td.id_tribunal) AS total_tribunales
            FROM defensas_mg AS d
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            LEFT JOIN tribunales_defensa AS td
                ON td.id_expediente = d.id_expediente
                AND td.etapa = d.etapa
                AND td.estado = 'vigente'
                AND td.asignacion_vigente = 1
            WHERE d.estado = 'programada'
                AND DATEDIFF(
                    d.fecha,
                    CURDATE()
                ) BETWEEN 0 AND :dias
            GROUP BY
                d.id_defensa,
                d.id_expediente,
                d.etapa,
                d.fecha,
                u.nombre,
                u.apellido
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'dias'
                => $parametros['anticipacion_tribunal']
        ]);

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $fila
        ) {
            $requeridos = $fila['etapa'] === 'mg1'
                ? $parametros['tribunales_mg1']
                : $parametros['tribunales_mg2'];

            if (
                (int) $fila['total_tribunales']
                < $requeridos
            ) {
                $this->agregar(
                    $alertas,
                    'defensa_sin_tribunales',
                    (int) $fila['id_defensa'],
                    'alta',
                    'Defensa próxima sin tribunales suficientes',
                    $fila['nombre']
                        . ' '
                        . $fila['apellido']
                        . ' tiene una defensa próxima con '
                        . $fila['total_tribunales']
                        . ' tribunal(es).',
                    'controllers/defensa_ver.php?id='
                        . $fila['id_defensa']
                );
            }
        }

        // A8: Tutor con carga superior a la recomendada.
        $sql = "
            SELECT
                t.id_tutor,
                u.nombre,
                u.apellido,
                COUNT(a.id_asignacion) AS carga
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            INNER JOIN asignaciones_tutor AS a
                ON a.id_tutor = t.id_tutor
                AND a.estado = 'vigente'
                AND a.asignacion_vigente = 1
            GROUP BY
                t.id_tutor,
                u.nombre,
                u.apellido
            HAVING carga > :carga
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'carga' => $parametros['carga_tutor']
        ]);

        foreach (
            $consulta->fetchAll(PDO::FETCH_ASSOC)
            as $fila
        ) {
            $this->agregar(
                $alertas,
                'sobrecarga_tutor',
                (int) $fila['id_tutor'],
                'media',
                'Carga elevada de tutor',
                $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' tiene '
                    . $fila['carga']
                    . ' estudiantes asignados.',
                'controllers/tutores_listar.php'
            );
        }

        // A9: Defensa programada sin citaciones.
        $sql = "
            SELECT
                d.id_defensa,
                u.nombre,
                u.apellido,
                d.fecha
            FROM defensas_mg AS d
            INNER JOIN expedientes_mg AS e
                ON e.id_expediente = d.id_expediente
            INNER JOIN estudiantes AS es
                ON es.id_estudiante = e.id_estudiante
            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario
            WHERE d.estado = 'programada'
                AND NOT EXISTS (
                    SELECT 1
                    FROM documentos_generados AS dg
                    WHERE dg.id_defensa = d.id_defensa
                        AND dg.tipo IN (
                            'citacion_tribunal',
                            'citacion_estudiante'
                        )
                )
        ";

        foreach (
            $this->conexion->query($sql)->fetchAll(
                PDO::FETCH_ASSOC
            ) as $fila
        ) {
            $this->agregar(
                $alertas,
                'defensa_sin_citaciones',
                (int) $fila['id_defensa'],
                'media',
                'Defensa sin citaciones',
                'La defensa de '
                    . $fila['nombre']
                    . ' '
                    . $fila['apellido']
                    . ' todavía no tiene citaciones.',
                'controllers/citaciones_defensa.php?id='
                    . $fila['id_defensa']
            );
        }

        return $this->filtrarAtendidas($alertas);
    }

    /**
     * Oculta las alertas que ya fueron atendidas.
     */
    private function filtrarAtendidas(
        array $alertas
    ): array {
        $consulta = $this->conexion->prepare(
            "
                SELECT clave_alerta
                FROM alertas_atendidas
            "
        );

        $consulta->execute();

        $clavesAtendidas = $consulta->fetchAll(
            PDO::FETCH_COLUMN
        );

        return array_values(
            array_filter(
                $alertas,
                fn(array $alerta): bool =>
                    !in_array(
                        $alerta['clave'],
                        $clavesAtendidas,
                        true
                    )
            )
        );
    }

    /**
     * Marca una alerta como atendida.
     */
    public function marcarAtendida(
        string $tipo,
        int $idReferencia,
        string $clave,
        string $nota,
        int $atendidaPor
    ): bool {
        $sql = "
            INSERT INTO alertas_atendidas (
                tipo_alerta,
                id_referencia,
                clave_alerta,
                nota,
                atendida_por
            )
            VALUES (
                :tipo,
                :id_referencia,
                :clave,
                :nota,
                :atendida_por
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'tipo' => $tipo,
            'id_referencia' => $idReferencia,
            'clave' => $clave,
            'nota' => trim($nota),
            'atendida_por' => $atendidaPor
        ]);
    }
}