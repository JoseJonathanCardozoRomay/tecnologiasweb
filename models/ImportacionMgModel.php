<?php

class ImportacionMgModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra la cabecera de una importación.
     */
    public function crearImportacion(
        string $nombreArchivo,
        string $hashArchivo,
        int $registradoPor
    ): int {
        $sql = "
            INSERT INTO importaciones_mg (
                nombre_archivo,
                hash_archivo,
                estado,
                registrado_por
            )
            VALUES (
                :nombre_archivo,
                :hash_archivo,
                'procesando',
                :registrado_por
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'nombre_archivo' => $nombreArchivo,
            'hash_archivo' => $hashArchivo,
            'registrado_por' => $registradoPor
        ]);

        return (int) $this->conexion->lastInsertId();
    }

    /**
     * Comprueba si el mismo archivo ya fue procesado.
     */
    public function archivoProcesado(
        string $hashArchivo
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM importaciones_mg
            WHERE hash_archivo = :hash_archivo
                AND estado = 'completada'
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'hash_archivo' => $hashArchivo
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Busca las relaciones necesarias para crear un expediente.
     */
    public function buscarReferencias(
        string $registroUniversitario,
        string $modalidad,
        string $cohorte
    ): array {
        $sql = "
            SELECT
                es.id_estudiante,

                m.id_modalidad,
                m.requiere_tutor,

                c.id_cohorte

            FROM estudiantes AS es

            INNER JOIN usuarios AS u
                ON u.id_usuario = es.id_usuario

            CROSS JOIN modalidades_grado AS m
            CROSS JOIN cohortes_mg AS c

            WHERE es.registro_universitario
                = :registro_universitario

                AND u.estado = 'activo'

                AND (
                    m.codigo = :modalidad_codigo
                    OR m.nombre = :modalidad_nombre
                )

                AND m.activa = 1

                AND (
                    c.codigo = :cohorte_codigo
                    OR c.nombre = :cohorte_nombre
                )

                AND c.activa = 1

            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'registro_universitario'
                => $registroUniversitario,
            'modalidad_codigo' => strtoupper(
                $modalidad
            ),
            'modalidad_nombre' => $modalidad,
            'cohorte_codigo' => $cohorte,
            'cohorte_nombre' => $cohorte
        ]);

        $referencias = $consulta->fetch(
            PDO::FETCH_ASSOC
        );

        return $referencias ?: [];
    }

    /**
     * Comprueba si el expediente ya existe.
     */
    public function expedienteExiste(
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

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_estudiante' => $idEstudiante,
            'id_modalidad' => $idModalidad,
            'id_cohorte' => $idCohorte
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Crea el expediente y su primera etapa en una transacción.
     */
    public function crearExpediente(
        int $idEstudiante,
        int $idModalidad,
        int $idCohorte,
        ?string $tituloTrabajo,
        string $fechaInicio,
        int $registradoPor
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
                    :creado_por
                )
            ";

            $consultaExpediente = $this->conexion->prepare(
                $sqlExpediente
            );

            $consultaExpediente->execute([
                'id_estudiante' => $idEstudiante,
                'id_modalidad' => $idModalidad,
                'id_cohorte' => $idCohorte,
                'titulo_trabajo' => $tituloTrabajo,
                'fecha_inicio' => $fechaInicio,
                'creado_por' => $registradoPor
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

            $consultaEtapa = $this->conexion->prepare(
                $sqlEtapa
            );

            $consultaEtapa->execute([
                'id_expediente' => $idExpediente,
                'fecha_inicio' => $fechaInicio,
                'registrado_por' => $registradoPor
            ]);

            $this->conexion->commit();

            return $idExpediente;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }

    /**
     * Guarda el resultado individual de una fila.
     */
    public function registrarDetalle(
        int $idImportacion,
        int $numeroFila,
        array $datos,
        string $resultado,
        string $mensaje,
        ?int $idExpediente = null
    ): void {
        $sql = "
            INSERT INTO importaciones_mg_detalle (
                id_importacion,
                numero_fila,
                registro_universitario,
                nombres,
                apellidos,
                correo,
                carrera,
                semestre,
                modalidad,
                cohorte,
                resultado,
                mensaje,
                datos_originales,
                id_expediente
            )
            VALUES (
                :id_importacion,
                :numero_fila,
                :registro_universitario,
                :nombres,
                :apellidos,
                :correo,
                :carrera,
                :semestre,
                :modalidad,
                :cohorte,
                :resultado,
                :mensaje,
                :datos_originales,
                :id_expediente
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_importacion' => $idImportacion,
            'numero_fila' => $numeroFila,
            'registro_universitario'
                => $datos['registro_universitario'] ?? null,
            'nombres' => $datos['nombres'] ?? null,
            'apellidos' => $datos['apellidos'] ?? null,
            'correo' => $datos['correo'] ?? null,
            'carrera' => $datos['carrera'] ?? null,
            'semestre' => $datos['semestre'] ?? null,
            'modalidad' => $datos['modalidad'] ?? null,
            'cohorte' => $datos['cohorte'] ?? null,
            'resultado' => $resultado,
            'mensaje' => $mensaje,
            'datos_originales' => json_encode(
                $datos,
                JSON_UNESCAPED_UNICODE
            ),
            'id_expediente' => $idExpediente
        ]);
    }

    /**
     * Finaliza la importación con sus totales.
     */
    public function finalizar(
        int $idImportacion,
        int $total,
        int $correctas,
        int $advertencias,
        int $errores,
        int $creadas,
        bool $fallida = false
    ): void {
        $sql = "
            UPDATE importaciones_mg
            SET
                estado = :estado,
                total_filas = :total,
                filas_correctas = :correctas,
                filas_advertencia = :advertencias,
                filas_error = :errores,
                filas_creadas = :creadas,
                fecha_procesamiento = NOW()
            WHERE id_importacion = :id_importacion
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'estado' => $fallida
                ? 'fallida'
                : 'completada',
            'total' => $total,
            'correctas' => $correctas,
            'advertencias' => $advertencias,
            'errores' => $errores,
            'creadas' => $creadas,
            'id_importacion' => $idImportacion
        ]);
    }

    /**
     * Lista las importaciones realizadas.
     */
    public function listar(): array
    {
        $sql = "
            SELECT
                i.*,
                u.nombre,
                u.apellido
            FROM importaciones_mg AS i
            INNER JOIN usuarios AS u
                ON u.id_usuario = i.registrado_por
            ORDER BY i.fecha_registro DESC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    /**
     * Lista el resultado de cada fila.
     */
    public function listarDetalle(
        int $idImportacion
    ): array {
        $sql = "
            SELECT *
            FROM importaciones_mg_detalle
            WHERE id_importacion = :id_importacion
            ORDER BY numero_fila ASC
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_importacion' => $idImportacion
        ]);

        return $consulta->fetchAll(
            PDO::FETCH_ASSOC
        );
    }
}