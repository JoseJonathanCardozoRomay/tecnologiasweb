<?php

require_once __DIR__ . '/BitacoraModel.php';

class DocumentoMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Las plantillas pueden corregirse sin alterar documentos ya emitidos. */
    public function listarPlantillas(): array
    {
        $consulta = $this->conexion->query("
            SELECT id_plantilla, codigo, nombre, cuerpo_html,
                version, activa, fecha_actualizacion
            FROM plantillas_documento
            ORDER BY nombre ASC
        ");

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarPlantilla(
        int $idPlantilla,
        string $cuerpo,
        int $idUsuario
    ): string {
        $cuerpo = trim($cuerpo);
        if (
            $idPlantilla < 1
            || $idUsuario < 1
            || $cuerpo === ''
            || mb_strlen($cuerpo) > 10000
        ) {
            return 'datos_invalidos';
        }

        preg_match_all('/\{\{([a-z_]+)\}\}/', $cuerpo, $coincidencias);
        $permitidas = [
            'destinatario', 'orden_tribunal', 'estudiante_nombre',
            'registro_universitario', 'carrera', 'modalidad',
            'titulo_trabajo', 'tema', 'tutor_nombre', 'numero_carta',
            'fecha_larga', 'cohorte', 'etapa', 'fecha_defensa',
            'horario', 'ambiente', 'numero_documento', 'referencia_decanatura'
        ];

        if (array_diff($coincidencias[1], $permitidas) !== []) {
            return 'variable_invalida';
        }

        try {
            $this->conexion->beginTransaction();
            $consulta = $this->conexion->prepare("
                SELECT codigo, version
                FROM plantillas_documento
                WHERE id_plantilla = :id_plantilla
                LIMIT 1
                FOR UPDATE
            ");
            $consulta->execute(['id_plantilla' => $idPlantilla]);
            $actual = $consulta->fetch(PDO::FETCH_ASSOC);

            if (!$actual) {
                $this->conexion->rollBack();
                return 'no_encontrada';
            }

            $actualizar = $this->conexion->prepare("
                UPDATE plantillas_documento
                SET cuerpo_html = :cuerpo,
                    version = version + 1,
                    actualizado_por = :id_usuario
                WHERE id_plantilla = :id_plantilla
            ");
            $actualizar->execute([
                'cuerpo' => $cuerpo,
                'id_usuario' => $idUsuario,
                'id_plantilla' => $idPlantilla
            ]);

            $bitacora = new BitacoraModel($this->conexion);
            $bitacora->registrar(
                $idUsuario,
                'plantilla_actualizada',
                'plantillas_documento',
                $idPlantilla,
                ['codigo' => $actual['codigo'], 'version' => (int) $actual['version']],
                ['codigo' => $actual['codigo'], 'version' => (int) $actual['version'] + 1]
            );

            $this->conexion->commit();
            return 'guardada';
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log('Error al actualizar plantilla: ' . $error->getMessage());
            return 'error';
        }
    }

    /**
     * Obtiene una plantilla activa.
     */
    private function buscarPlantilla(
        string $codigo
    ): ?array {
        $sql = "
            SELECT
                id_plantilla,
                codigo,
                cuerpo_html
            FROM plantillas_documento
            WHERE codigo = :codigo
                AND activa = 1
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'codigo' => $codigo
        ]);

        $plantilla = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $plantilla ?: null;
    }

    /**
     * Obtiene el siguiente número correlativo con bloqueo.
     */
    private function siguienteCorrelativo(
        string $tipo
    ): string {
        $anio = (int) date('Y');

        $sqlBuscar = "
            SELECT ultimo_numero
            FROM contadores_documento
            WHERE tipo = :tipo
                AND anio = :anio
            LIMIT 1
            FOR UPDATE
        ";

        $consultaBuscar = $this->conexion->prepare(
            $sqlBuscar
        );

        $consultaBuscar->execute([
            'tipo' => $tipo,
            'anio' => $anio
        ]);

        $numeroActual = $consultaBuscar->fetchColumn();

        if ($numeroActual === false) {
            $numeroNuevo = 1;

            $sqlInsertar = "
                INSERT INTO contadores_documento (
                    tipo,
                    anio,
                    ultimo_numero
                )
                VALUES (
                    :tipo,
                    :anio,
                    :numero
                )
            ";

            $consultaInsertar = $this->conexion->prepare(
                $sqlInsertar
            );

            $consultaInsertar->execute([
                'tipo' => $tipo,
                'anio' => $anio,
                'numero' => $numeroNuevo
            ]);
        } else {
            $numeroNuevo = (int) $numeroActual + 1;

            $sqlActualizar = "
                UPDATE contadores_documento
                SET ultimo_numero = :numero
                WHERE tipo = :tipo
                    AND anio = :anio
            ";

            $consultaActualizar = $this->conexion->prepare(
                $sqlActualizar
            );

            $consultaActualizar->execute([
                'numero' => $numeroNuevo,
                'tipo' => $tipo,
                'anio' => $anio
            ]);
        }

        $prefijos = [
            'citacion_tribunal' => 'CIT-TRIB',
            'citacion_estudiante' => 'CIT-EST',
            'carta_tutor' => 'CARTA'
        ];

        $prefijo = $prefijos[$tipo] ?? 'DOC';

        return $prefijo
            . '-'
            . $anio
            . '-'
            . str_pad(
                (string) $numeroNuevo,
                4,
                '0',
                STR_PAD_LEFT
            );
    }

    /**
     * Reemplaza únicamente las variables permitidas.
     */
    private function completarPlantilla(
        string $plantilla,
        array $datos
    ): string {
        $reemplazos = [];

        foreach ($datos as $variable => $valor) {
            $reemplazos[
                '{{' . $variable . '}}'
            ] = htmlspecialchars(
                (string) $valor,
                ENT_QUOTES,
                'UTF-8'
            );
        }

        return strtr(
            $plantilla,
            $reemplazos
        );
    }

    /**
     * Comprueba si la defensa ya tiene citaciones.
     */
    public function defensaTieneCitaciones(
        int $idDefensa
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM documentos_generados
            WHERE id_defensa = :id_defensa
                AND tipo IN (
                    'citacion_tribunal',
                    'citacion_estudiante'
                )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_defensa' => $idDefensa
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Genera las citaciones del estudiante y sus tribunales.
     */
    public function generarCitaciones(
        int $idDefensa,
        int $generadoPor
    ): int {
        try {
            $this->conexion->beginTransaction();

            $sqlDefensa = "
                SELECT
                    d.id_defensa,
                    d.id_expediente,
                    d.etapa,
                    d.fecha,
                    d.hora_inicio,
                    d.hora_fin,
                    d.ambiente,
                    d.estado,

                    e.titulo_trabajo,

                    u.nombre,
                    u.apellido,

                    m.nombre AS modalidad

                FROM defensas_mg AS d

                INNER JOIN expedientes_mg AS e
                    ON e.id_expediente = d.id_expediente

                INNER JOIN estudiantes AS es
                    ON es.id_estudiante = e.id_estudiante

                INNER JOIN usuarios AS u
                    ON u.id_usuario = es.id_usuario

                INNER JOIN modalidades_grado AS m
                    ON m.id_modalidad = e.id_modalidad

                WHERE d.id_defensa = :id_defensa
                LIMIT 1
                FOR UPDATE
            ";

            $consultaDefensa = $this->conexion->prepare(
                $sqlDefensa
            );

            $consultaDefensa->execute([
                'id_defensa' => $idDefensa
            ]);

            $defensa = $consultaDefensa->fetch(
                PDO::FETCH_ASSOC
            );

            if (
                !$defensa
                || !in_array(
                    $defensa['estado'],
                    ['programada', 'realizada'],
                    true
                )
                || $this->defensaTieneCitaciones($idDefensa)
            ) {
                $this->conexion->rollBack();
                return 0;
            }

            $plantillaTribunal = $this->buscarPlantilla(
                'CITACION_TRIBUNAL'
            );

            $plantillaEstudiante = $this->buscarPlantilla(
                'CITACION_ESTUDIANTE'
            );

            if (
                !$plantillaTribunal
                || !$plantillaEstudiante
            ) {
                $this->conexion->rollBack();
                return 0;
            }

            $sqlTribunales = "
                SELECT
                    td.orden,
                    u.nombre,
                    u.apellido

                FROM tribunales_defensa AS td

                INNER JOIN tutores AS t
                    ON t.id_tutor = td.id_tutor

                INNER JOIN usuarios AS u
                    ON u.id_usuario = t.id_usuario

                WHERE td.id_expediente = :id_expediente
                    AND td.etapa = :etapa
                    AND td.estado = 'vigente'
                    AND td.asignacion_vigente = 1

                ORDER BY td.orden ASC
            ";

            $consultaTribunales = $this->conexion->prepare(
                $sqlTribunales
            );

            $consultaTribunales->execute([
                'id_expediente'
                    => $defensa['id_expediente'],
                'etapa' => $defensa['etapa']
            ]);

            $tribunales = $consultaTribunales->fetchAll(
                PDO::FETCH_ASSOC
            );

            if (empty($tribunales)) {
                $this->conexion->rollBack();
                return 0;
            }

            $datosBase = [
                'estudiante_nombre' => $defensa['nombre']
                    . ' '
                    . $defensa['apellido'],
                'modalidad' => $defensa['modalidad'],
                'titulo_trabajo' => $defensa['titulo_trabajo']
                    ?: 'Sin título registrado',
                'etapa' => strtoupper(
                    $defensa['etapa']
                ),
                'fecha_defensa' => date(
                    'd/m/Y',
                    strtotime($defensa['fecha'])
                ),
                'horario' => substr(
                    $defensa['hora_inicio'],
                    0,
                    5
                )
                    . ' - '
                    . substr(
                        $defensa['hora_fin'],
                        0,
                        5
                    ),
                'ambiente' => $defensa['ambiente']
            ];

            $totalGenerado = 0;

            // Citación para cada tribunal
            foreach ($tribunales as $tribunal) {
                $numero = $this->siguienteCorrelativo(
                    'citacion_tribunal'
                );

                $destinatario = $tribunal['nombre']
                    . ' '
                    . $tribunal['apellido'];

                $contenido = $this->completarPlantilla(
                    $plantillaTribunal['cuerpo_html'],
                    array_merge(
                        $datosBase,
                        [
                            'destinatario' => $destinatario,
                            'orden_tribunal'
                                => $tribunal['orden'],
                            'numero_documento' => $numero
                        ]
                    )
                );

                $this->guardarDocumento(
                    (int) $plantillaTribunal['id_plantilla'],
                    (int) $defensa['id_expediente'],
                    $idDefensa,
                    'citacion_tribunal',
                    $destinatario,
                    $numero,
                    $contenido,
                    $generadoPor
                );

                $totalGenerado++;
            }

            // Citación para el estudiante
            $numeroEstudiante = $this->siguienteCorrelativo(
                'citacion_estudiante'
            );

            $destinatarioEstudiante = $defensa['nombre']
                . ' '
                . $defensa['apellido'];

            $contenidoEstudiante = $this->completarPlantilla(
                $plantillaEstudiante['cuerpo_html'],
                array_merge(
                    $datosBase,
                    [
                        'destinatario'
                            => $destinatarioEstudiante,
                        'numero_documento'
                            => $numeroEstudiante
                    ]
                )
            );

            $this->guardarDocumento(
                (int) $plantillaEstudiante['id_plantilla'],
                (int) $defensa['id_expediente'],
                $idDefensa,
                'citacion_estudiante',
                $destinatarioEstudiante,
                $numeroEstudiante,
                $contenidoEstudiante,
                $generadoPor
            );

            $totalGenerado++;

            $this->conexion->commit();

            return $totalGenerado;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Guarda el contenido definitivo de un documento.
     */
    private function guardarDocumento(
        int $idPlantilla,
        int $idExpediente,
        ?int $idDefensa,
        string $tipo,
        string $destinatario,
        string $numero,
        string $contenido,
        int $generadoPor
    ): void {
        $sql = "
            INSERT INTO documentos_generados (
                id_plantilla,
                id_expediente,
                id_defensa,
                tipo,
                destinatario,
                numero_correlativo,
                contenido_snapshot,
                generado_por
            )
            VALUES (
                :id_plantilla,
                :id_expediente,
                :id_defensa,
                :tipo,
                :destinatario,
                :numero_correlativo,
                :contenido_snapshot,
                :generado_por
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_plantilla' => $idPlantilla,
            'id_expediente' => $idExpediente,
            'id_defensa' => $idDefensa,
            'tipo' => $tipo,
            'destinatario' => $destinatario,
            'numero_correlativo' => $numero,
            'contenido_snapshot' => $contenido,
            'generado_por' => $generadoPor
        ]);
    }

    /** Emite una carta inmutable dentro de la transacción de asignación. */
    public function generarCartaParaAsignacion(int $idCarta, int $idUsuario): int
    {
        if (!$this->conexion->inTransaction()) {
            throw new LogicException('La carta requiere una asignación en curso.');
        }

        $plantilla = $this->buscarPlantilla('CARTA_TUTOR');
        if (!$plantilla) {
            throw new RuntimeException('Falta la plantilla de carta de tutor. Ejecuta la migración 016.');
        }

        $consulta = $this->conexion->prepare("
            SELECT c.id_carta, a.id_expediente, a.referencia_decanatura,
                ut.nombre AS nombre_tutor, ut.apellido AS apellido_tutor,
                ue.nombre AS nombre_estudiante, ue.apellido AS apellido_estudiante,
                e.registro_universitario, cr.nombre_carrera, x.titulo_trabajo,
                m.nombre AS modalidad, ch.nombre AS cohorte
            FROM cartas_designacion_mg AS c
            INNER JOIN asignaciones_tutor AS a ON a.id_asignacion = c.id_asignacion
            INNER JOIN tutores AS t ON t.id_tutor = a.id_tutor
            INNER JOIN usuarios AS ut ON ut.id_usuario = t.id_usuario
            INNER JOIN expedientes_mg AS x ON x.id_expediente = a.id_expediente
            INNER JOIN estudiantes AS e ON e.id_estudiante = x.id_estudiante
            INNER JOIN carreras AS cr ON cr.id_carrera = e.id_carrera
            INNER JOIN usuarios AS ue ON ue.id_usuario = e.id_usuario
            INNER JOIN modalidades_grado AS m ON m.id_modalidad = x.id_modalidad
            INNER JOIN cohortes_mg AS ch ON ch.id_cohorte = x.id_cohorte
            WHERE c.id_carta = :id_carta LIMIT 1
        ");
        $consulta->execute(['id_carta' => $idCarta]);
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);
        if (!$datos) {
            throw new RuntimeException('La carta solicitada no existe.');
        }

        $numero = $this->siguienteCorrelativo('carta_tutor');
        $estudiante = $datos['nombre_estudiante'] . ' ' . $datos['apellido_estudiante'];
        $tutor = $datos['nombre_tutor'] . ' ' . $datos['apellido_tutor'];
        $contenido = $this->completarPlantilla($plantilla['cuerpo_html'], [
            'estudiante_nombre' => $estudiante,
            'registro_universitario' => $datos['registro_universitario'] ?? '',
            'carrera' => $datos['nombre_carrera'],
            'modalidad' => $datos['modalidad'],
            'tema' => $datos['titulo_trabajo'] ?? '',
            'titulo_trabajo' => $datos['titulo_trabajo'] ?? '',
            'tutor_nombre' => $tutor,
            'destinatario' => $tutor,
            'numero_carta' => $numero,
            'numero_documento' => $numero,
            'fecha_larga' => date('d/m/Y'),
            'cohorte' => $datos['cohorte'],
            'referencia_decanatura' => $datos['referencia_decanatura']
        ]);

        $this->guardarDocumento(
            (int) $plantilla['id_plantilla'],
            (int) $datos['id_expediente'],
            null,
            'carta_tutor',
            $tutor,
            $numero,
            $contenido,
            $idUsuario
        );
        $idDocumento = (int) $this->conexion->lastInsertId();
        $actualizar = $this->conexion->prepare("
            UPDATE cartas_designacion_mg
            SET numero_carta = :numero
            WHERE id_carta = :id_carta AND numero_carta IS NULL
        ");
        $actualizar->execute(['numero' => $numero, 'id_carta' => $idCarta]);
        return $idDocumento;
    }

    public function buscarCartaPorNumero(string $numero): ?array
    {
        if ($numero === '') {
            return null;
        }
        $consulta = $this->conexion->prepare("
            SELECT id_documento, numero_correlativo
            FROM documentos_generados
            WHERE tipo = 'carta_tutor' AND numero_correlativo = :numero
            LIMIT 1
        ");
        $consulta->execute(['numero' => $numero]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Lista los documentos generados para una defensa.
     */
    public function listarPorDefensa(
        int $idDefensa
    ): array {
        $sql = "
            SELECT
                id_documento,
                tipo,
                destinatario,
                numero_correlativo,
                fecha_generacion
            FROM documentos_generados
            WHERE id_defensa = :id_defensa
            ORDER BY fecha_generacion ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_defensa' => $idDefensa
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Obtiene el contenido original de un documento.
     */
    public function buscarPorId(
        int $idDocumento
    ): ?array {
        $sql = "
            SELECT
                id_documento,
                id_expediente,
                id_defensa,
                tipo,
                destinatario,
                numero_correlativo,
                contenido_snapshot,
                fecha_generacion
            FROM documentos_generados
            WHERE id_documento = :id_documento
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_documento' => $idDocumento
        ]);

        $documento = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $documento ?: null;
    }

    /** Lista los documentos emitidos para el personal de gestión. */
public function listarGenerados(string $busqueda = ''): array
{
    $sql = "
        SELECT d.id_documento, d.tipo, d.destinatario,
               d.numero_correlativo, d.fecha_generacion,
               u.nombre AS estudiante_nombre,
               u.apellido AS estudiante_apellido
        FROM documentos_generados AS d
        INNER JOIN expedientes_mg AS e
            ON e.id_expediente = d.id_expediente
        INNER JOIN estudiantes AS es
            ON es.id_estudiante = e.id_estudiante
        INNER JOIN usuarios AS u
            ON u.id_usuario = es.id_usuario
    ";

    $parametros = [];

    if ($busqueda !== '') {
        $sql .= "
            WHERE d.numero_correlativo LIKE :numero
               OR d.destinatario LIKE :destinatario
               OR u.nombre LIKE :nombre
               OR u.apellido LIKE :apellido
        ";

        $valor = '%' . $busqueda . '%';
        $parametros = [
            'numero' => $valor,
            'destinatario' => $valor,
            'nombre' => $valor,
            'apellido' => $valor
        ];
    }

    $sql .= "
        ORDER BY d.fecha_generacion DESC, d.id_documento DESC
        LIMIT 200
    ";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute($parametros);

    return $consulta->fetchAll(PDO::FETCH_ASSOC);
}
}
