<?php
/**
 * Exportador XLSX liviano y sin dependencias externas.
 *
 * El sistema no utiliza Composer, por lo que generamos directamente el paquete
 * Office Open XML mediante ZipArchive. Esto permite entregar un .xlsx real y
 * mantener la exportación independiente de bibliotecas externas.
 */

declare(strict_types=1);

/**
 * Genera un libro Excel .xlsx de una sola hoja a partir de encabezados y filas.
 *
 * @param string[] $headers Encabezados de las columnas.
 * @param array<int, array<int, scalar|null>> $rows Filas de datos.
 * @param array<string, string> $metadata Metadatos opcionales del reporte.
 * @return string Ruta temporal del XLSX generado.
 */
function crearExcelXlsx(array $headers, array $rows, array $metadata = []): string
{
    if (!extension_loaded('zip')) {
        throw new RuntimeException('La extensión ZIP de PHP es necesaria para generar archivos Excel.');
    }

    $tmpDir = sys_get_temp_dir();
    $base = tempnam($tmpDir, 'tutorias_xlsx_');
    if ($base === false) {
        throw new RuntimeException('No se pudo crear el archivo temporal para el reporte Excel.');
    }

    $xlsxPath = $base . '.xlsx';
    @unlink($base);

    $zip = new ZipArchive();
    if ($zip->open($xlsxPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('No se pudo construir el archivo Excel.');
    }

    $lastRow = 4 + count($rows);
    $lastCol = indiceColumnaExcel(count($headers) - 1);

    $zip->addFromString('[Content_Types].xml', contenidoTipos($lastRow > 4));
    $zip->addFromString('_rels/.rels', contenidoRelsRaiz());
    $zip->addFromString('xl/workbook.xml', contenidoWorkbook());
    $zip->addFromString('xl/_rels/workbook.xml.rels', contenidoWorkbookRels());
    $zip->addFromString('xl/styles.xml', contenidoStyles());
    $zip->addFromString('xl/worksheets/sheet1.xml', contenidoHoja($headers, $rows, $metadata, $lastCol, $lastRow));

    $zip->close();

    return $xlsxPath;
}

/**
 * Convierte un índice 0-based a letras de columna Excel (A, B, ..., AA...).
 */
function indiceColumnaExcel(int $indice): string
{
    $columna = '';
    for ($i = $indice + 1; $i > 0; $i = intdiv($i - 1, 26)) {
        $columna = chr(65 + (($i - 1) % 26)) . $columna;
    }
    return $columna;
}

/** Escapa texto para XML de forma segura. */
function xmlEscapar(mixed $valor): string
{
    $texto = (string) $valor;
    // Si el reporte recibe texto heredado con mojibake, usamos el mismo
    // reparador global antes de generar el XML para que el Excel tampoco
    // muestre nombres corruptos.
    if (function_exists('repararMojibake')) {
        $texto = repararMojibake($texto);
    }
    return htmlspecialchars($texto, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

/** Construye una celda de texto como inlineStr para evitar sharedStrings. */
function celdaTexto(string $referencia, mixed $valor, int $estilo = 0): string
{
    $texto = xmlEscapar($valor === null || $valor === '' ? '—' : $valor);
    return '<c r="' . $referencia . '" s="' . $estilo . '" t="inlineStr"><is><t xml:space="preserve">' . $texto . '</t></is></c>';
}

function filaXml(int $fila, array $valores, int $estilo = 0): string
{
    $xml = '<row r="' . $fila . '">';
    foreach (array_values($valores) as $i => $valor) {
        $xml .= celdaTexto(indiceColumnaExcel($i) . $fila, $valor, $estilo);
    }
    return $xml . '</row>';
}

function contenidoTipos(bool $conDatos): string
{
    $extra = $conDatos
        ? ''
        : '';
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
        . '</Types>' . $extra;
}

function contenidoRelsRaiz(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';
}

function contenidoWorkbook(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Auditoría" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';
}

function contenidoWorkbookRels(): string
{
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
        . '</Relationships>';
}

function contenidoStyles(): string
{
    // Estilos institucionales: encabezado azul, subtítulo gris y badges de estado.
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<numFmts count="0"/>'
        . '<fonts count="3">'
        . '<font><sz val="11"/><name val="Aptos"/></font>'
        . '<font><b/><sz val="15"/><color rgb="FFFFFFFF"/><name val="Aptos Display"/></font>'
        . '<font><b/><sz val="11"/><name val="Aptos"/></font>'
        . '</fonts>'
        . '<fills count="4">'
        . '<fill><patternFill patternType="none"/></fill>'
        . '<fill><patternFill patternType="gray125"/></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FF003B5C"/><bgColor indexed="64"/></patternFill></fill>'
        . '<fill><patternFill patternType="solid"><fgColor rgb="FFEAF2F7"/><bgColor indexed="64"/></patternFill></fill>'
        . '</fills>'
        . '<borders count="2">'
        . '<border><left/><right/><top/><bottom/></border>'
        . '<border><left style="thin"><color rgb="FFD9E2E8"/></left><right style="thin"><color rgb="FFD9E2E8"/></right><top style="thin"><color rgb="FFD9E2E8"/></top><bottom style="thin"><color rgb="FFD9E2E8"/></bottom></border>'
        . '</borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="4">'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" applyAlignment="1"><alignment vertical="center"/></xf>'
        . '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>'
        . '<xf numFmtId="0" fontId="2" fillId="3" borderId="1" applyAlignment="1"><alignment vertical="center"/></xf>'
        . '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf>'
        . '</cellXfs>'
        . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
        . '</styleSheet>';
}

function contenidoHoja(array $headers, array $rows, array $metadata, string $lastCol, int $lastRow): string
{
    $titulo = xmlEscapar($metadata['titulo'] ?? 'Reporte de auditoría');
    $desde = xmlEscapar($metadata['desde'] ?? 'TODAS');
    $hasta = xmlEscapar($metadata['hasta'] ?? 'TODAS');
    $generado = xmlEscapar($metadata['generado'] ?? date('Y-m-d H:i:s'));

    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="5" topLeftCell="A6" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
        . '<sheetFormatPr defaultRowHeight="20"/>'
        . '<cols>'
        . '<col min="1" max="1" width="20" customWidth="1"/>'
        . '<col min="2" max="2" width="24" customWidth="1"/>'
        . '<col min="3" max="3" width="18" customWidth="1"/>'
        . '<col min="4" max="4" width="22" customWidth="1"/>'
        . '<col min="5" max="5" width="14" customWidth="1"/>'
        . '<col min="6" max="6" width="55" customWidth="1"/>'
        . '<col min="7" max="7" width="16" customWidth="1"/>'
        . '</cols>'
        . '<sheetData>'
        . '<row r="1" ht="26" customHeight="1"><c r="A1" s="1" t="inlineStr"><is><t>' . $titulo . '</t></is></c></row>'
        . '<row r="2"><c r="A2" t="inlineStr"><is><t>Filtro desde</t></is></c><c r="B2" t="inlineStr"><is><t>' . $desde . '</t></is></c></row>'
        . '<row r="3"><c r="A3" t="inlineStr"><is><t>Filtro hasta</t></is></c><c r="B3" t="inlineStr"><is><t>' . $hasta . '</t></is></c></row>'
        . '<row r="4"><c r="A4" t="inlineStr"><is><t>Generado</t></is></c><c r="B4" t="inlineStr"><is><t>' . $generado . '</t></is></c></row>'
        . filaXml(5, $headers, 1);

    $fila = 6;
    foreach ($rows as $row) {
        $xml .= filaXml($fila, $row, 3);
        $fila++;
    }

    $xml .= '</sheetData>';
    if ($lastRow >= 5) {
        $xml .= '<autoFilter ref="A5:' . $lastCol . $lastRow . '"/>';
    }
    $xml .= '<pageMargins left="0.25" right="0.25" top="0.5" bottom="0.5" header="0.2" footer="0.2"/>'
        . '</worksheet>';

    return $xml;
}
