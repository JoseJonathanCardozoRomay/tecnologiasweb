<?php
/**
 * Generador de PDF mínimo y sin dependencias externas (PHP puro).
 *
 * El proyecto no usa Composer ni librerías de terceros, por lo que este
 * archivo implementa lo justo para generar documentos A4 imprimibles:
 * tipografías base Type1 (Helvetica), salto de línea, ajuste de texto,
 * líneas divisorias y paginación automática.
 *
 * El PDF resultante es un archivo real (no HTML renombrado), de modo que
 * pasa la validación de integridad de includes/archivos.php y puede
 * descargarse o imprimirse desde el navegador.
 */

const PDF_ANCHO = 595.28;
const PDF_ALTO = 841.89;
const PDF_MARGEN = 56.7;

class PdfDocumento
{
    private array $paginas = [];
    private array $fuentes = ['F1' => 'Helvetica', 'F2' => 'Helvetica-Bold', 'F3' => 'Helvetica-Oblique'];
    private int $indicePagina = -1;
    private float $cursorY;
    private float $anchoUtil;
    private float $altoUtil;

    public function __construct(
        private string $titulo = 'Documento',
        private string $autor = 'Sistema de Tutorías',
        private float $margen = PDF_MARGEN
    ) {
        $this->anchoUtil = PDF_ANCHO - ($margen * 2);
        $this->altoUtil = PDF_ALTO - ($margen * 2);
        $this->nuevaPagina();
    }

    public function anchoUtil(): float
    {
        return $this->anchoUtil;
    }

    /** Coordenada vertical actual (distancia desde arriba de la página). */
    public function posicionY(): float
    {
        return $this->cursorY;
    }

    public function moverY(float $y): void
    {
        $this->cursorY = $y;
    }

    public function margen(): float
    {
        return $this->margen;
    }

    public function nuevaPagina(): void
    {
        $this->paginas[] = [];
        $this->indicePagina = count($this->paginas) - 1;
        $this->cursorY = PDF_ALTO - $this->margen;
    }

    public function espacio(float $alto): void
    {
        $this->cursorY -= $alto;
    }

    /** Dibuja una línea de texto. Devuelve el ancho consumido. */
    public function texto(string $texto, float $tamano = 10.5, string $fuente = 'F1', float $x = null, float $y = null): float
    {
        $texto = self::escapar($texto);
        $y = $y ?? $this->cursorY;
        $x = $x ?? $this->margen;
        $operador = "BT /{$fuente} " . self::numero($tamano) . " Tf " . self::numero($x) . ' ' . self::numero($y) . " Td ({$texto}) Tj ET";
        $this->paginas[$this->indicePagina][] = $operador;
        if ($y === $this->cursorY) {
            $this->cursorY -= $tamano * 1.35;
        }

        return self::anchoTexto($texto, $tamano, $fuente);
    }

    public function textoDerecha(string $texto, float $tamano, float $derecha, string $fuente = 'F1'): void
    {
        $ancho = self::anchoTexto($texto, $tamano, $fuente);
        $this->texto($texto, $tamano, $fuente, $derecha - $ancho, $this->cursorY);
    }

    public function centrado(string $texto, float $tamano = 10.5, string $fuente = 'F1'): void
    {
        $ancho = self::anchoTexto($texto, $tamano, $fuente);
        $this->texto($texto, $tamano, $fuente, (PDF_ANCHO - $ancho) / 2, $this->cursorY);
    }

    /** Texto justificado a ambos márgenes, con salto de línea y paginación. */
    public function parrafo(string $texto, float $tamano = 10.5, string $fuente = 'F1', float $interlineado = 1.55, float $sangria = 0.0): void
    {
        $lineas = self::ajustar($texto, $this->anchoUtil - $sangria, $tamano, $fuente);
        $total = count($lineas);
        $altoLinea = $tamano * $interlineado;

        foreach ($lineas as $i => $linea) {
            /* Reserva el espacio de la línea siguiente para no dejar huérfana
               una única línea al comienzo de una página. */
            $reservar = $i < $total - 1 ? $altoLinea * 2 : $altoLinea;
            $this->asegurarEspacio($reservar);
            $this->texto($linea, $tamano, $fuente, $this->margen + $sangria, $this->cursorY);
            $this->moverY($this->cursorY - $altoLinea);
        }

        $this->moverY($this->cursorY - $tamano * 0.35);
    }

    /**
     * Dibuja texto en coordenadas absolutas sin alterar el cursor.
     * Se usa para bloques de varias líneas en la misma fila.
     */
    public function textoFijo(string $texto, float $tamano, float $x, float $y, string $fuente = 'F1'): float
    {
        $texto = self::escapar($texto);
        $this->paginas[$this->indicePagina][] = "BT /{$fuente} " . self::numero($tamano)
            . ' Tf ' . self::numero($x) . ' ' . self::numero($y) . " Td ({$texto}) Tj ET";

        return self::anchoTexto($texto, $tamano, $fuente);
    }

    /**
     * Línea de separación horizontal.
     *
     * @param bool $avanzar false dibuja la regla sin mover el cursor, útil para
     *                      firmar varias columnas en la misma altura.
     */
    public function regla(float $grosor = 0.8, string $color = '0.05 0.17 0.29', float $margenIzq = null, float $margenDer = null, bool $avanzar = true): void
    {
        $izq = $margenIzq ?? $this->margen;
        $der = $margenDer ?? (PDF_ANCHO - $this->margen);
        if ($avanzar) {
            $this->asegurarEspacio($grosor + 8);
            $this->cursorY -= 6;
        }
        $this->paginas[$this->indicePagina][] = sprintf(
            '%s w %s 0 0 %s %s %s m %s %s l S',
            $color,
            self::numero($grosor),
            self::numero($der - $izq),
            self::numero($izq),
            self::numero($this->cursorY),
            self::numero($der),
            self::numero($this->cursorY)
        );
        if ($avanzar) {
            $this->cursorY -= 12;
        }
    }

    /**
     * Pie de página institucional. Se dibuja en la última página ya utilizada,
     * abre una nueva solo si el contenido no deja espacio suficiente.
     *
     * @param float   $derecha Coordenada X del margen derecho.
     * @param array   $lineas  Línea izquierda, línea derecha y nota al pie.
     */
    public function pie(float $derecha, array $lineas = []): void
    {
        $alto = 40;
        if ($this->cursorY - $alto < $this->margen) {
            $this->nuevaPagina();
        }

        $y = $this->margen + 24;
        $this->paginas[$this->indicePagina][] = sprintf(
            '0.8 0.82 0.85 w 0.6 0 0 0.6 %s %s m %s %s l S',
            self::numero($this->margen),
            self::numero($y + 10),
            self::numero($derecha),
            self::numero($y + 10)
        );

        $izquierda = trim((string) ($lineas[0] ?? ''));
        $derechaTxt = trim((string) ($lineas[1] ?? ''));
        if ($izquierda !== '') {
            $this->texto($izquierda, 7.5, 'F1', $this->margen, $y);
        }
        if ($derechaTxt !== '') {
            $this->textoDerecha($derechaTxt, 7.5, $derecha);
        }

        $nota = trim((string) ($lineas[2] ?? ''));
        if ($nota !== '') {
            $this->texto($nota, 7, 'F3', $this->margen, $y - 12);
        }
    }

    public function asegurarEspacio(float $altoNecesario): void
    {
        if ($this->cursorY - $altoNecesario < $this->margen) {
            $this->nuevaPagina();
        }
    }

    /** Devuelve el PDF completo como cadena binaria. */
    public function salida(): string
    {
        $numPaginas = count($this->paginas);
        $objetos = [];
        $idsFuente = [];

        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objetos[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';

        $fontRefs = '';
        $nextFontId = 4;
        foreach ($this->fuentes as $clave => $baseFont) {
            $idsFuente[$clave] = $nextFontId;
            $objetos[$nextFontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /' . $baseFont . ' /Encoding /WinAnsiEncoding >>';
            $fontRefs .= '/' . $clave . ' ' . $nextFontId . ' 0 R ';
            $nextFontId++;
        }

        $kids = '';
        $pageIds = [];
        $nextPageId = $nextFontId;
        $contentIds = [];
        $nextContentId = $nextPageId;
        foreach ($this->paginas as $contenido) {
            $pageIds[] = $nextPageId;
            $contentIds[] = $nextContentId;
            $kids .= $nextPageId . ' 0 R ';
            $nextPageId++;
            $nextContentId++;
        }

        $objetos[2] = '<< /Type /Pages /Kids [' . trim($kids) . '] /Count ' . $numPaginas . ' >>';

        foreach ($this->paginas as $i => $contenido) {
            $stream = implode("\n", $contenido);
            $objetos[$pageIds[$i]] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %s %s] /Resources << /Font << %s>> >> /Contents %d 0 R >>',
                self::numero(PDF_ANCHO),
                self::numero(PDF_ALTO),
                $fontRefs,
                $contentIds[$i]
            );
            $objetos[$contentIds[$i]] = "<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";
        }

        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';

        ksort($objetos);
        $maxId = max(array_keys($objetos));

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objetos as $id => $cuerpo) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $cuerpo . "\nendobj\n";
        }

        $inicioXref = strlen($pdf);
        $pdf .= "xref\n0 " . ($maxId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($id = 1; $id <= $maxId; $id++) {
            if (isset($offsets[$id])) {
                $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
            } else {
                $pdf .= "0000000000 65535 f \n";
            }
        }
        unset($objetos);

        $pdf .= "trailer\n<< /Size " . ($maxId + 1) . " /Root 1 0 R /Info << /Title (" . self::escapar($this->titulo) . ") /Author (" . self::escapar($this->autor) . ") /Producer (Sistema de Tutorias) >> >>\n";
        $pdf .= "startxref\n" . $inicioXref . "\n%%EOF\n";

        return $pdf;
    }

    public static function anchoTexto(string $texto, float $tamano, string $fuente = 'F1'): float
    {
        $tabla = $fuente === 'F2' ? self::anchosNegrita() : self::anchos();
        $ancho = 0.0;
        $len = strlen($texto);
        for ($i = 0; $i < $len; $i++) {
            $code = ord($texto[$i]);
            $ancho += $tabla[$code] ?? 556;
        }

        return ($ancho / 1000) * $tamano;
    }

    /** Ajuste de línea por palabras. */
    public static function ajustar(string $texto, float $anchoMax, float $tamano, string $fuente = 'F1'): array
    {
        $texto = preg_replace('/\s+/u', ' ', trim($texto)) ?? '';
        if ($texto === '') {
            return [''];
        }
        $palabras = explode(' ', $texto);
        $lineas = [];
        $actual = '';
        foreach ($palabras as $palabra) {
            $prueba = $actual === '' ? $palabra : $actual . ' ' . $palabra;
            if (self::anchoTexto($prueba, $tamano, $fuente) <= $anchoMax || $actual === '') {
                $actual = $prueba;
            } else {
                $lineas[] = $actual;
                $actual = $palabra;
            }
        }
        if ($actual !== '') {
            $lineas[] = $actual;
        }

        return $lineas;
    }

    /** UTF-8 -> Windows-1252 (WinAnsiEncoding) y escape de literales PDF. */
    public static function escapar(string $texto): string
    {
        $texto = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $texto);
        if (function_exists('mb_convert_encoding')) {
            $texto = mb_convert_encoding($texto, 'Windows-1252', 'UTF-8');
        } else {
            $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n']);
        }
        $texto = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', '?', $texto) ?? $texto;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texto);
    }

    public static function numero(float $n): string
    {
        $s = number_format($n, 2, '.', '');

        return rtrim(rtrim($s, '0'), '.') ?: '0';
    }

    private static function anchos(): array
    {
        static $t = null;
        if ($t !== null) {
            return $t;
        }
        $t = array_fill(0, 256, 556);
        $mapa = array_fill(0, 256, 556);
        $datos = [
            32 => 278, 33 => 278, 34 => 355, 35 => 556, 36 => 556, 37 => 889, 38 => 667, 39 => 191,
            40 => 333, 41 => 333, 42 => 389, 43 => 584, 44 => 278, 45 => 333, 46 => 278, 47 => 278,
            48 => 556, 49 => 556, 50 => 556, 51 => 556, 52 => 556, 53 => 556, 54 => 556, 55 => 556,
            56 => 556, 57 => 556, 58 => 278, 59 => 278, 60 => 584, 61 => 584, 62 => 584, 63 => 556,
            64 => 1015, 65 => 667, 66 => 667, 67 => 722, 68 => 722, 69 => 667, 70 => 611, 71 => 778,
            72 => 722, 73 => 278, 74 => 500, 75 => 667, 76 => 556, 77 => 833, 78 => 722, 79 => 778,
            80 => 667, 81 => 778, 82 => 722, 83 => 667, 84 => 611, 85 => 722, 86 => 667, 87 => 944,
            88 => 667, 89 => 667, 90 => 611, 91 => 278, 92 => 278, 93 => 278, 94 => 469, 95 => 556,
            96 => 333, 97 => 556, 98 => 556, 99 => 500, 100 => 556, 101 => 556, 102 => 278, 103 => 556,
            104 => 556, 105 => 222, 106 => 222, 107 => 500, 108 => 222, 109 => 833, 110 => 556,
            111 => 556, 112 => 556, 113 => 556, 114 => 333, 115 => 500, 116 => 278, 117 => 556,
            118 => 500, 119 => 722, 120 => 500, 121 => 500, 122 => 500, 123 => 334, 124 => 260,
            125 => 334, 126 => 584,
        ];
        foreach ($datos as $code => $w) {
            $mapa[$code] = $w;
        }

        return $mapa;
    }

    private static function anchosNegrita(): array
    {
        static $t = null;
        if ($t !== null) {
            return $t;
        }
        $mapa = array_fill(0, 256, 556);
        $datos = [
            32 => 278, 33 => 333, 34 => 474, 35 => 556, 36 => 556, 37 => 889, 38 => 722, 39 => 238,
            40 => 333, 41 => 333, 42 => 389, 43 => 584, 44 => 278, 45 => 333, 46 => 278, 47 => 278,
            48 => 556, 49 => 556, 50 => 556, 51 => 556, 52 => 556, 53 => 556, 54 => 556, 55 => 556,
            56 => 556, 57 => 556, 58 => 333, 59 => 333, 60 => 584, 61 => 584, 62 => 584, 63 => 611,
            64 => 975, 65 => 722, 66 => 722, 67 => 722, 68 => 722, 69 => 667, 70 => 611, 71 => 778,
            72 => 722, 73 => 278, 74 => 556, 75 => 722, 76 => 611, 77 => 833, 78 => 722, 79 => 778,
            80 => 667, 81 => 778, 82 => 722, 83 => 667, 84 => 611, 85 => 722, 86 => 667, 87 => 944,
            88 => 667, 89 => 667, 90 => 611, 91 => 333, 92 => 278, 93 => 333, 94 => 584, 95 => 556,
            96 => 333, 97 => 556, 98 => 611, 99 => 556, 100 => 611, 101 => 556, 102 => 333, 103 => 611,
            104 => 611, 105 => 278, 106 => 278, 107 => 556, 108 => 278, 109 => 889, 110 => 611,
            111 => 611, 112 => 611, 113 => 611, 114 => 389, 115 => 556, 116 => 333, 117 => 611,
            118 => 556, 119 => 778, 120 => 556, 121 => 556, 122 => 500, 123 => 389, 124 => 280,
            125 => 389, 126 => 584,
        ];
        foreach ($datos as $code => $w) {
            $mapa[$code] = $w;
        }

        return $mapa;
    }
}
