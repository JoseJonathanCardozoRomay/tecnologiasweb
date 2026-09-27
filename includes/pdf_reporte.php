<?php

class PdfReporte
{
    private $paginas = [];
    private $lineas = [];
    private $y = 0;

    public function __construct()
    {
        $this->nuevaPagina();
    }

    public function nuevaPagina()
    {
        if (!empty($this->lineas)) {
            $this->paginas[] = $this->lineas;
        }
        $this->lineas = [];
        $this->y = 780;
    }

    public function espacio($altura = 12)
    {
        $this->y -= $altura;
    }

    public function titulo($texto)
    {
        $this->lineas[] = ['fuente' => 'F2', 'tamano' => 17, 'texto' => $texto, 'y' => $this->y];
        $this->y -= 24;
    }

    public function subtitulo($texto)
    {
        $this->lineas[] = ['fuente' => 'F1', 'tamano' => 10, 'texto' => $texto, 'y' => $this->y];
        $this->y -= 15;
    }

    public function seccion($texto)
    {
        $this->y -= 6;
        $this->lineas[] = ['fuente' => 'F2', 'tamano' => 11, 'texto' => mb_strtoupper($texto, 'UTF-8'), 'y' => $this->y];
        $this->y -= 8;
        $this->lineas[] = ['rect' => [56, $this->y, 484, 0.8, [0.11, 0.17, 0.29]]];
        $this->y -= 14;
    }

    public function campo($etiqueta, $valor)
    {
        $this->lineas[] = ['fuente' => 'F2', 'tamano' => 9, 'texto' => $etiqueta, 'y' => $this->y];
        $this->y -= 13;
        foreach ($this->envolver((string) $valor, 88) as $linea) {
            $this->lineas[] = ['fuente' => 'F1', 'tamano' => 10, 'texto' => $linea, 'y' => $this->y];
            $this->y -= 14;
        }
        $this->y -= 4;
    }

    public function parrafo($texto, $sangria = 0)
    {
        foreach ($this->envolver((string) $texto, 84) as $linea) {
            $this->lineas[] = ['fuente' => 'F1', 'tamano' => 10, 'texto' => ($sangria === 0 ? '' : '    ') . $linea, 'y' => $this->y];
            $this->y -= 14;
        }
        $this->y -= 4;
    }

    public function tabla(array $filas, array $anchos)
    {
        foreach ($filas as $fila) {
            $x = 56;
            foreach ($fila as $i => $celda) {
                $this->lineas[] = [
                    'fuente' => 'F1',
                    'tamano' => 9,
                    'texto'  => $this->recortar((string) $celda, $anchos[$i] ?? 40),
                    'y'      => $this->y,
                    'x'      => $x,
                ];
                $x += ($anchos[$i] ?? 40) + 6;
            }
            $this->y -= 14;
        }
        $this->y -= 4;
    }

    public function pie($texto)
    {
        $this->lineas[] = ['fuente' => 'F1', 'tamano' => 8, 'texto' => $texto, 'y' => 40, 'x' => 56];
    }

    private function envolver($texto, $columnas)
    {
        $texto = trim(preg_replace('/\s+/u', ' ', (string) $texto));
        if ($texto === '') {
            return [''];
        }
        if (function_exists('mb_strwidth')) {
            return str_split($texto, $columnas);
        }

        return str_split($texto, $columnas);
    }

    private function recortar($texto, $maximo)
    {
        $texto = trim((string) $texto);
        if (function_exists('mb_strwidth') && mb_strwidth($texto, 'UTF-8') > $maximo) {
            return mb_substr($texto, 0, max(1, $maximo - 1), 'UTF-8') . '.';
        }
        if (strlen($texto) > $maximo) {
            return substr($texto, 0, max(1, $maximo - 1)) . '.';
        }

        return $texto;
    }

    private function escapar($texto)
    {
        $texto = (string) $texto;
        $utf8 = preg_match('//u', $texto) === 1;
        $texto = $utf8
            ? preg_replace('/[\x{80}-\x{FF}]/u', '?', $texto)
            : preg_replace('/[\x80-\xFF]/', '?', $texto);

        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], $texto);
    }

    public function salida()
    {
        if (!empty($this->lineas)) {
            $this->paginas[] = $this->lineas;
        }
        if (empty($this->paginas)) {
            $this->paginas[] = [];
        }

        $objetos = [];
        $paginaIds = [];
        $fuentes = [];

        foreach ($this->paginas as $indice => $lineas) {
            $idPagina = 4 + ($indice * 2);
            $idContenido = $idPagina + 1;
            $paginaIds[] = $idPagina;

            $flujo = "BT\n";
            foreach ($lineas as $linea) {
                if (isset($linea['rect'])) {
                    $flujo = "Q " . $linea['rect'][3] . " w " . $this->color($linea['rect'][4]) . " RG\n"
                        . $linea['rect'][0] . ' ' . $linea['rect'][1] . ' m '
                        . ($linea['rect'][0] + $linea['rect'][2]) . ' ' . $linea['rect'][1] . ' l S Q' . "\n";
                    continue;
                }
                $fuentes[$linea['fuente']] = true;
                $flujo .= '/' . $linea['fuente'] . ' ' . $linea['tamano'] . ' Tf\n'
                    . '1 0 0 1 ' . ($linea['x'] ?? 56) . ' ' . $linea['y'] . ' Tm\n'
                    . '(' . $this->escapar($linea['texto']) . ") Tj\n";
            }
            $flujo .= "ET\n";

            $flujo .= "0.92 0.93 0.95 rg 56 806 484 0.6 re f\n";
            $flujo .= $this->color([0.11, 0.17, 0.29]) . ' rg 56 762 484 30 re f' . "\n";
            $flujo .= "BT /F2 12 Tf 1 1 1 rg 68 772 Tm (Universidad Privada Domingo Savio) Tj ET\n";
            $flujo .= "BT /F1 9 Tf 1 1 1 rg 68 750 Tm (Sede Tarija) Tj ET\n";
            $flujo .= "BT /F1 9 Tf 1 1 1 rg 470 772 Tm (Tecnologias Web) Tj ET\n";
            $flujo .= "BT /F1 8 Tf 0.4 0.45 0.48 rg 400 40 Tm (Pagina " . ($indice + 1) . ' de ' . count($this->paginas) . ") Tj ET\n";

            $objetos[$idContenido] = "<< /Length " . strlen($flujo) . " >>\nstream\n" . $flujo . "endstream";
            $objetos[$idPagina] = "<< /Type /Page /Parent 3 0 R /MediaBox [0 0 595 842] "
                . "/Resources << /Font << /F1 1 0 R /F2 2 0 R >> >> /Contents " . $idContenido . " 0 R >>";
        }

        $objetos[1] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
        $objetos[2] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>";
        $objetos[3] = "<< /Type /Pages /Kids [" . implode(' ', array_map(fn($id) => $id . ' 0 R', $paginaIds)) . "] /Count " . count($paginaIds) . " >>";
        $objetos[5 + (count($this->paginas) * 2)] = "<< /Type /Catalog /Pages 3 0 R >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objetos as $id => $cuerpo) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $cuerpo . "\nendobj\n";
        }

        $total = max(array_keys($objetos));
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . ($total + 1) . "\n0000000000 65535 f \n";
        for ($id = 1; $id <= $total; $id++) {
            $pdf .= isset($offsets[$id])
                ? sprintf("%010d 00000 n \n", $offsets[$id])
                : "0000000000 65535 f \n";
        }
        $pdf .= "trailer\n<< /Size " . ($total + 1) . " /Root " . $total . " 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

        return $pdf;
    }

    private function color(array $rgb)
    {
        return sprintf('%.3F %.3F %.3F', $rgb[0], $rgb[1], $rgb[2]);
    }
}
