<?php

class DocReporte
{
    private $titulo;
    private $subtitulo;
    private $cuerpo = [];

    public function __construct($titulo = '', $subtitulo = '')
    {
        $this->titulo = (string) $titulo;
        $this->subtitulo = (string) $subtitulo;
    }

    public function agregar($tipo, $contenido)
    {
        if ($tipo === 'seccion') {
            $this->cuerpo[] = '<p class="seccion">' . $this->escapar($contenido) . '</p>';
            return;
        }

        if ($tipo === 'campos') {
            $filas = '';
            foreach ((array) $contenido as $etiqueta => $valor) {
                $filas .= '<tr>'
                    . '<td class="etiqueta">' . $this->escapar($etiqueta) . '</td>'
                    . '<td class="valor">' . nl2br($this->escapar($valor)) . '</td>'
                    . '</tr>';
            }
            $this->cuerpo[] = '<table class="datos">' . $filas . '</table>';
            return;
        }

        $this->cuerpo[] = '<p class="parrafo">' . nl2br($this->escapar($contenido)) . '</p>';
    }

    public function cerrar($texto)
    {
        $this->cuerpo[] = '<p class="cierre">' . $this->escapar($texto) . '</p>';
    }

    public function salida()
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office"'
            . ' xmlns:w="urn:schemas-microsoft-com:office:word"'
            . ' xmlns="http://www.w3.org/TR/REC-html40">'
            . '<head><meta charset="utf-8">'
            . '<title>' . $this->escapar($this->titulo) . '</title>'
            . '<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View>'
            . '<w:Zoom>100</w:Zoom></w:WordDocument></xml><![endif]-->'
            . '<style>'
            . '@page{size:21cm 29.7cm;margin:2.5cm 2cm;}'
            . 'body{font-family:Calibri,Arial,sans-serif;font-size:11pt;color:#1c2b4a;}'
            . 'h1{font-family:Cambria,Georgia,serif;font-size:18pt;color:#1c2b4a;margin:0 0 4pt 0;}'
            . '.subtitulo{font-size:10pt;color:#55637a;margin:0 0 18pt 0;}'
            . '.seccion{font-family:Cambria,Georgia,serif;font-size:12pt;color:#1c2b4a;'
            . 'margin:16pt 0 6pt 0;border-bottom:1px solid #1c2b4a;padding-bottom:2pt;}'
            . 'table.datos{width:100%;border-collapse:collapse;margin:0 0 6pt 0;}'
            . 'table.datos td{border:1px solid #d5dae4;padding:5pt 7pt;font-size:10pt;vertical-align:top;}'
            . 'table.datos td.etiqueta{background:#eef1f6;font-weight:bold;width:38%;}'
            . '.parrafo{font-size:11pt;line-height:1.5;margin:0 0 8pt 0;text-align:justify;}'
            . '.cierre{font-size:9pt;color:#55637a;font-style:italic;margin-top:18pt;'
            . 'border-top:1px solid #d5dae4;padding-top:8pt;}'
            . '</style></head><body>'
            . '<h1>' . $this->escapar($this->titulo) . '</h1>'
            . '<p class="subtitulo">' . $this->escapar($this->subtitulo) . '</p>'
            . implode('', $this->cuerpo)
            . '</body></html>';

        return "\xEF\xBB\xBF" . $html;
    }

    private function escapar($valor)
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }
}
