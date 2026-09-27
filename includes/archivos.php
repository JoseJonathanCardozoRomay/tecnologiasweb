<?php
/**
 * Utilidades de validación de archivos subidos y de archivos almacenados.
 *
 * Se divide en dos responsabilidades:
 *  - validate_uploaded_document(): valida un $_FILES recién subido (extensión,
 *    MIME real detectado con fileinfo, tamaño y firma/binario del formato).
 *  - verify_stored_file(): comprueba que un archivo guardado en disco exista,
 *    sea legible, no esté vacío y tenga el tamaño esperado. Se usa antes de
 *    servir descargas o previsualizaciones para no entregar archivos corruptos.
 */

if (!function_exists('document_upload_rules')) {
    /**
     * Mapa extension => MIMEs aceptados para los documentos del expediente.
     */
    function document_upload_rules(): array
    {
        return [
            'pdf' => [
                'application/pdf',
                'application/x-pdf',
            ],
            'doc' => [
                'application/msword',
                'application/x-msword',
                'application/vnd.ms-word',
                'application/x-ole-storage',
                'application/CDFV2',
                'application/CDFV2-corrupt',
            ],
            'docx' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-word.document.macroEnabled.12',
                'application/zip',
            ],
        ];
    }
}

if (!function_exists('document_upload_accepted_attr')) {
    /**
     * Valor para el atributo accept del <input type="file">.
     */
    function document_upload_accepted_attr(): string
    {
        return '.pdf,.doc,.docx,application/pdf,application/msword,'
            . 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }
}

if (!function_exists('detect_upload_mime')) {
    /**
     * MIME real detectado por contenido. Nevertrust del nombre del archivo.
     */
    function detect_upload_mime(string $rutaTemporal): string
    {
        if (!is_file($rutaTemporal) || !is_readable($rutaTemporal)) {
            return '';
        }

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo !== false) {
                $mime = finfo_file($finfo, $rutaTemporal);
                finfo_close($finfo);
                if (is_string($mime) && $mime !== '') {
                    return strtolower(trim($mime));
                }
            }
        }

        return strtolower((string) mime_content_type($rutaTemporal));
    }
}

if (!function_exists('document_extension_matches_mime')) {
    /**
     * Confirma que el MIME detectado concuerda con la extensión declarada.
     *
     * Un único MIME válido se acepta cuando es el esperado para esa extensión
     * o cuando el contenedor es genérico (los .doc antiguos y algunos .docx
     * se reportan como application/zip, application/octet-stream o
     * application/CDFV2 según el detector).
     */
    function document_extension_matches_mime(string $extension, string $mimeDetectado): bool
    {
        $reglas = document_upload_rules();
        if (!isset($reglas[$extension])) {
            return false;
        }

        $mimeDetectado = strtolower(trim($mimeDetectado));
        if ($mimeDetectado === '') {
            return false;
        }

        if (in_array($mimeDetectado, $reglas[$extension], true)) {
            return true;
        }

        $genericos = [
            'application/octet-stream',
            'application/zip',
            'application/x-zip-compressed',
            'application/x-ole-storage',
            'application/CDFV2',
            'application/CDFV2-corrupt',
            'application/x-tika-msoffice',
        ];

        return in_array($mimeDetectado, $genericos, true);
    }
}

if (!function_exists('validate_uploaded_document')) {
    /**
     * Valida un archivo subido del expediente.
     *
     * @param array $archivo   Entrada de $_FILES.
     * @param int   $maxBytes  Tamaño máximo permitido en bytes.
     * @return array{lista: string[], mime: string, extension: string}
     */
    function validate_uploaded_document(array $archivo, int $maxBytes): array
    {
        $errores = [];
        $extension = strtolower((string) pathinfo((string) ($archivo['name'] ?? ''), PATHINFO_EXTENSION));
        $mime = '';

        $codigo = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($codigo !== UPLOAD_ERR_OK) {
            return [
                'lista' => [describe_upload_error($codigo, $maxBytes)],
                'mime' => '',
                'extension' => $extension,
            ];
        }

        if (!in_array($extension, array_keys(document_upload_rules()), true)) {
            $errores[] = 'Solo se permiten documentos .doc, .docx o .pdf.';
            return ['lista' => $errores, 'mime' => '', 'extension' => $extension];
        }

        $size = (int) ($archivo['size'] ?? 0);
        if ($size <= 0) {
            $errores[] = 'El archivo está vacío.';
        } elseif ($size > $maxBytes) {
            $errores[] = 'El archivo no puede superar los ' . round($maxBytes / 1048576, 1) . ' MB.';
        }

        $tmp = (string) ($archivo['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            $errores[] = 'No se pudo leer el archivo subido. Volvé a intentarlo.';
            return ['lista' => $errores, 'mime' => '', 'extension' => $extension];
        }

        $mime = detect_upload_mime($tmp);
        if (!document_extension_matches_mime($extension, $mime)) {
            $errores[] = 'El contenido del archivo no corresponde a un ' . strtoupper($extension)
                . ' válido. Descargá el documento desde tu programa y volvé a subirlo.';
        }

        if ($extension === 'pdf' && !pdf_is_structurally_valid($tmp)) {
            $errores[] = 'El PDF está dañado o incompleto. Volvé a exportarlo antes de subirlo.';
        }

        return ['lista' => $errores, 'mime' => $mime, 'extension' => $extension];
    }
}

if (!function_exists('describe_upload_error')) {
    /**
     * Traduce los códigos de error de PHP a un mensaje accionable.
     */
    function describe_upload_error(int $codigo, int $maxBytes): string
    {
        switch ($codigo) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo supera el tamaño máximo permitido de '
                    . round($maxBytes / 1048576, 1) . ' MB.';
            case UPLOAD_ERR_PARTIAL:
                return 'La subida se interrumpió. Volvé a intentarlo.';
            case UPLOAD_ERR_NO_FILE:
                return 'Debes seleccionar un archivo .doc, .docx o .pdf.';
            case UPLOAD_ERR_NO_TMP_DIR:
            case UPLOAD_ERR_CANT_WRITE:
                return 'No se pudo guardar el archivo en el servidor. Intentá de nuevo en unos minutos.';
            case UPLOAD_ERR_EXTENSION:
                return 'Una extensión de PHP bloqueó la subida. Contactá al administrador.';
            default:
                return 'No se pudo subir el archivo. Volvé a intentarlo.';
        }
    }
}

if (!function_exists('pdf_is_structurally_valid')) {
    /**
     * Verifica que un PDF tenga cabecera, trailer y catálogo:xref legibles.
     * Detecta truncamientos y archivos vacíos antes de servirlos.
     */
    function pdf_is_structurally_valid(string $ruta): bool
    {
        if (!is_file($ruta) || !is_readable($ruta)) {
            return false;
        }

        $tamano = filesize($ruta);
        if ($tamano === false || $tamano < 32) {
            return false;
        }

        $manejador = fopen($ruta, 'rb');
        if ($manejador === false) {
            return false;
        }

        $cabecera = (string) fread($manejador, 5);
        if ($cabecera !== '%PDF-') {
            fclose($manejador);
            return false;
        }

        fseek($manejador, -1024, SEEK_END);
        $cola = (string) fread($manejador, 1024);
        fclose($manejador);

        if (strpos($cola, '%%EOF') === false) {
            return false;
        }

        return strpos($cola, 'startxref') !== false;
    }
}

if (!function_exists('verify_stored_file')) {
    /**
     * Comprueba la integridad de un archivo guardado antes de descargarlo o
     * previsualizarlo.
     *
     * @param string      $ruta      Ruta relativa al proyecto (ej. uploads/...).
     * @param string      $carpeta   Carpeta raíz autorizada.
     * @param int|null    $minBytes  Tamaño mínimo legible.
     * @return array{ok: bool, motivo: string, bytes: int, ruta_absoluta: string}
     */
    function verify_stored_file(string $ruta, string $carpeta = 'uploads', ?int $minBytes = 1): array
    {
        $fallo = ['ok' => false, 'motivo' => '', 'bytes' => 0, 'ruta_absoluta' => ''];
        $ruta = trim($ruta);

        if ($ruta === '') {
            $fallo['motivo'] = 'El documento no tiene una ruta de archivo asociada.';
            return $fallo;
        }

        if (strpos($ruta, "\0") !== false || strpos($ruta, '..') !== false) {
            $fallo['motivo'] = 'La ruta del documento no es válida.';
            return $fallo;
        }

        $base = realpath(__DIR__ . '/../' . trim($carpeta, '/'));
        $absoluta = realpath(__DIR__ . '/../' . ltrim($ruta, '/'));

        if ($base === false || $absoluta === false) {
            $fallo['motivo'] = 'El archivo no se encuentra en el servidor.';
            return $fallo;
        }

        if (strpos($absoluta, $base . DIRECTORY_SEPARATOR) !== 0) {
            $fallo['motivo'] = 'La ruta del documento está fuera de la carpeta autorizada.';
            return $fallo;
        }

        if (!is_file($absoluta)) {
            $fallo['motivo'] = 'El archivo no se encuentra en el servidor.';
            return $fallo;
        }

        if (!is_readable($absoluta)) {
            $fallo['motivo'] = 'El archivo existe pero no se puede leer. Contactá al administrador.';
            return $fallo;
        }

        $bytes = (int) filesize($absoluta);
        if ($bytes < max(1, (int) $minBytes)) {
            $fallo['motivo'] = 'El archivo está vacío o incompleto.';
            return $fallo;
        }

        $extension = strtolower((string) pathinfo($absoluta, PATHINFO_EXTENSION));
        if ($extension === 'pdf' && !pdf_is_structurally_valid($absoluta)) {
            $fallo['motivo'] = 'El PDF está dañado o incompleto. Volvé a cargarlo.';
            return $fallo;
        }

        return ['ok' => true, 'motivo' => '', 'bytes' => $bytes, 'ruta_absoluta' => $absoluta];
    }
}

if (!function_exists('stored_file_http_headers')) {
    /**
     * Cabeceras seguras para servir un archivo descargado.
     */
    function stored_file_http_headers(string $nombreSugerido, string $mime, bool $inline): array
    {
        $nombreSugerido = preg_replace('/[^A-Za-z0-9._\- ]/', '_', $nombreSugerido) ?: 'documento';
        $disposicion = $inline ? 'inline' : 'attachment';

        return [
            'Content-Type' => $mime,
            'Content-Length' => '0',
            'Content-Disposition' => $disposicion . '; filename="' . $nombreSugerido . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
        ];
    }
}

if (!function_exists('mime_de_archivo')) {
    /**
     * MIME de un archivo en disco, detectado por contenido con respaldo por
     * extensión para los formatos conocidos.
     */
    function mime_de_archivo(string $rutaAbsoluta): string
    {
        $detectado = detect_upload_mime($rutaAbsoluta);
        if ($detectado !== '' && $detectado !== 'application/octet-stream') {
            return $detectado;
        }

        $porExtension = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'txt' => 'text/plain; charset=utf-8',
        ];

        $extension = strtolower((string) pathinfo($rutaAbsoluta, PATHINFO_EXTENSION));

        return $porExtension[$extension] ?? 'application/octet-stream';
    }
}

if (!function_exists('es_peticion_json')) {
    /**
     * Detecta si el cliente espera JSON para poder responder con un error legible.
     */
    function es_peticion_json(): bool
    {
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            return true;
        }

        $accept = strtolower((string) ($_SERVER['HTTP_ACCEPT'] ?? ''));

        return strpos($accept, 'application/json') !== false;
    }
}

if (!function_exists('responder_error_archivo')) {
    /**
     * Responde con el error adecuado según el transporte (JSON o HTML).
     */
    function responder_error_archivo(int $codigo, string $mensaje): void
    {
        http_response_code($codigo);

        if (es_peticion_json()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'status' => 'error',
                'codigo' => $codigo,
                'message' => $mensaje,
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $pagina = __DIR__ . '/../views/errores/' . $codigo . '.php';
        if (is_file($pagina)) {
            $motivo = $mensaje;
            require $pagina;
            exit;
        }

        header('Content-Type: text/plain; charset=utf-8');
        echo $mensaje;
        exit;
    }
}

if (!function_exists('servir_archivo_almacenado')) {
    /**
     * Sirve un archivo verificado, ya sea en línea (previsualización) o como
     * descarga. Devuelve un error legible si el archivo no pasa la validación.
     *
     * @param array  $verificacion Resultado de verify_stored_file().
     * @param string $nombre       Nombre sugerido para el navegador.
     * @param bool   $inline       true para previsualizar, false para descargar.
     */
    function servir_archivo_almacenado(array $verificacion, string $nombre, bool $inline): void
    {
        if (empty($verificacion['ok'])) {
            $motivo = (string) ($verificacion['motivo'] ?? 'El archivo no está disponible.');
            responder_error_archivo(404, $motivo);
        }

        $rutaAbsoluta = (string) $verificacion['ruta_absoluta'];
        $mime = mime_de_archivo($rutaAbsoluta);

        $extension = strtolower((string) pathinfo($nombre, PATHINFO_EXTENSION));
        if ($extension === '') {
            $extension = strtolower((string) pathinfo($rutaAbsoluta, PATHINFO_EXTENSION));
        }
        $nombre = pathinfo($nombre, PATHINFO_FILENAME) . '.' . $extension;

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (int) $verificacion['bytes']);
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . str_replace('"', '', $nombre) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        header('Pragma: no-cache');

        readfile($rutaAbsoluta);
        exit;
    }
}
