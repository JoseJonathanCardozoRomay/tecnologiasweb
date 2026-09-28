<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__
    . '/../models/ImportacionMgModel.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirPermiso(
    'mg.importaciones.crear',
    '../index.php'
);

$modeloImportacion = new ImportacionMgModel($pdo);
$usuarioSesion = obtenerUsuarioSesion();

$error = '';
$resultadoImportacion = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $archivo = $_FILES['archivo_csv'] ?? null;

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página.';
    } elseif (
        !$archivo
        || $archivo['error'] !== UPLOAD_ERR_OK
    ) {
        $error = 'Selecciona un archivo CSV válido.';
    } elseif (
        $archivo['size'] <= 0
        || $archivo['size'] > 2 * 1024 * 1024
    ) {
        $error = 'El archivo debe pesar como máximo 2 MB.';
    } elseif (
        strtolower(
            pathinfo(
                $archivo['name'],
                PATHINFO_EXTENSION
            )
        ) !== 'csv'
    ) {
        $error = 'El archivo debe tener extensión CSV.';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $tipoMime = $finfo->file(
            $archivo['tmp_name']
        );

        $tiposPermitidos = [
            'text/csv',
            'text/plain',
            'application/csv',
            'application/vnd.ms-excel',
            'application/octet-stream'
        ];

        if (
            !in_array(
                $tipoMime,
                $tiposPermitidos,
                true
            )
        ) {
            $error = 'El tipo de archivo no está permitido.';
        } else {
            $hashArchivo = hash_file(
                'sha256',
                $archivo['tmp_name']
            );

            if (
                $modeloImportacion->archivoProcesado(
                    $hashArchivo
                )
            ) {
                $error = 'Este archivo ya fue procesado anteriormente.';
            } else {
                $manejador = fopen(
                    $archivo['tmp_name'],
                    'rb'
                );

                if ($manejador === false) {
                    $error = 'No fue posible leer el archivo.';
                } else {
                    // Detectamos automáticamente coma o punto y coma.
                    $primeraLinea = fgets($manejador);

                    $delimitador = substr_count(
                        $primeraLinea,
                        ';'
                    ) > substr_count(
                        $primeraLinea,
                        ','
                    ) ? ';' : ',';

                    rewind($manejador);

                    $encabezados = fgetcsv(
                        $manejador,
                        0,
                        $delimitador
                    );

                    if (!$encabezados) {
                        fclose($manejador);
                        $error = 'El archivo no contiene encabezados.';
                    } else {
                        $encabezados = array_map(
                            function ($encabezado): string {
                                $encabezado = trim(
                                    (string) $encabezado
                                );

                                // Eliminamos el BOM de archivos UTF-8.
                                $encabezado = preg_replace(
                                    '/^\xEF\xBB\xBF/',
                                    '',
                                    $encabezado
                                );

                                return mb_strtolower(
                                    $encabezado
                                );
                            },
                            $encabezados
                        );

                        $columnasObligatorias = [
                            'registro_universitario',
                            'modalidad',
                            'cohorte',
                            'titulo_trabajo',
                            'fecha_inicio'
                        ];

                        $faltantes = array_diff(
                            $columnasObligatorias,
                            $encabezados
                        );

                        if (!empty($faltantes)) {
                            fclose($manejador);

                            $error = 'Faltan las columnas: '
                                . implode(', ', $faltantes)
                                . '.';
                        } else {
                            $idImportacion = null;

                            try {
                                $idImportacion = $modeloImportacion
                                    ->crearImportacion(
                                        basename(
                                            $archivo['name']
                                        ),
                                        $hashArchivo,
                                        (int) $usuarioSesion[
                                            'id_usuario'
                                        ]
                                    );

                                $total = 0;
                                $correctas = 0;
                                $advertencias = 0;
                                $errores = 0;
                                $creadas = 0;
                                $numeroFila = 1;

                                while (
                                    (
                                        $fila = fgetcsv(
                                            $manejador,
                                            0,
                                            $delimitador
                                        )
                                    ) !== false
                                ) {
                                    $numeroFila++;

                                    // Ignoramos filas completamente vacías.
                                    if (
                                        count(
                                            array_filter(
                                                $fila,
                                                fn($valor): bool =>
                                                    trim(
                                                        (string) $valor
                                                    ) !== ''
                                            )
                                        ) === 0
                                    ) {
                                        continue;
                                    }

                                    $total++;

                                    if ($total > 1000) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                [],
                                                'error',
                                                'Se superó el máximo de 1000 filas por archivo.'
                                            );
                                        break;
                                    }

                                    if (
                                        count($fila)
                                        !== count($encabezados)
                                    ) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                [],
                                                'error',
                                                'La cantidad de columnas no coincide con el encabezado.'
                                            );
                                        continue;
                                    }

                                    $datos = array_combine(
                                        $encabezados,
                                        array_map(
                                            fn($valor): string =>
                                                trim(
                                                    (string) $valor
                                                ),
                                            $fila
                                        )
                                    );

                                    $registro = $datos[
                                        'registro_universitario'
                                    ] ?? '';

                                    $modalidad = $datos[
                                        'modalidad'
                                    ] ?? '';

                                    $cohorte = $datos[
                                        'cohorte'
                                    ] ?? '';

                                    $titulo = $datos[
                                        'titulo_trabajo'
                                    ] ?? '';

                                    $fechaInicio = $datos[
                                        'fecha_inicio'
                                    ] ?? '';

                                    $fechaValida = DateTime
                                        ::createFromFormat(
                                            'Y-m-d',
                                            $fechaInicio
                                        );

                                    if (
                                        $registro === ''
                                        || $modalidad === ''
                                        || $cohorte === ''
                                    ) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'error',
                                                'Existen campos obligatorios vacíos.'
                                            );
                                        continue;
                                    }

                                    if (
                                        !$fechaValida
                                        || $fechaValida->format(
                                            'Y-m-d'
                                        ) !== $fechaInicio
                                    ) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'error',
                                                'La fecha debe utilizar el formato AAAA-MM-DD.'
                                            );
                                        continue;
                                    }

                                    if (
                                        mb_strlen($titulo) > 200
                                    ) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'error',
                                                'El título supera los 200 caracteres.'
                                            );
                                        continue;
                                    }

                                    $referencias = $modeloImportacion
                                        ->buscarReferencias(
                                            $registro,
                                            $modalidad,
                                            $cohorte
                                        );

                                    if (empty($referencias)) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'pendiente_cuenta',
                                                'No se encontró un estudiante, modalidad o cohorte activa que coincida.'
                                            );
                                        continue;
                                    }

                                    if (
                                        $modeloImportacion
                                            ->expedienteExiste(
                                                (int) $referencias[
                                                    'id_estudiante'
                                                ],
                                                (int) $referencias[
                                                    'id_modalidad'
                                                ],
                                                (int) $referencias[
                                                    'id_cohorte'
                                                ]
                                            )
                                    ) {
                                        $advertencias++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'omitida',
                                                'El expediente ya existe y no fue duplicado.'
                                            );
                                        continue;
                                    }

                                    try {
                                        $idExpediente = $modeloImportacion
                                            ->crearExpediente(
                                                (int) $referencias[
                                                    'id_estudiante'
                                                ],
                                                (int) $referencias[
                                                    'id_modalidad'
                                                ],
                                                (int) $referencias[
                                                    'id_cohorte'
                                                ],
                                                $titulo !== ''
                                                    ? $titulo
                                                    : null,
                                                $fechaInicio,
                                                (int) $usuarioSesion[
                                                    'id_usuario'
                                                ]
                                            );

                                        $correctas++;
                                        $creadas++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'creada',
                                                'Expediente creado correctamente.',
                                                $idExpediente
                                            );
                                    } catch (PDOException $e) {
                                        $errores++;

                                        $modeloImportacion
                                            ->registrarDetalle(
                                                $idImportacion,
                                                $numeroFila,
                                                $datos,
                                                'error',
                                                'No fue posible crear el expediente.'
                                            );
                                    }
                                }

                                fclose($manejador);

                                $modeloImportacion->finalizar(
                                    $idImportacion,
                                    $total,
                                    $correctas,
                                    $advertencias,
                                    $errores,
                                    $creadas
                                );

                                header(
                                    'Location: importaciones_detalle.php?id='
                                    . $idImportacion
                                );
                                exit;
                            } catch (Throwable $e) {
                                fclose($manejador);

                                if ($idImportacion !== null) {
                                    $modeloImportacion->finalizar(
                                        $idImportacion,
                                        0,
                                        0,
                                        0,
                                        1,
                                        0,
                                        true
                                    );
                                }

                                $error = 'No fue posible procesar la importación.';
                            }
                        }
                    }
                }
            }
        }
    }
}

$tituloPagina = 'Importar estudiantes desde CSV';
$rutaBase = '../';

require_once __DIR__
    . '/../views/importaciones/crear.php';