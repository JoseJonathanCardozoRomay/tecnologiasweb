# Genera los fixtures binarios que PHP no puede producir en este contenedor:
#   - imagenes JPEG  (requiere un codificador de imagen; no hay GD)
#   - documentos DOCX (requiere compresion ZIP; la extension zip no esta cargada)
#
# Uso desde la raiz del proyecto:
#   powershell -File database/fixtures/generar_binarios.ps1

$ErrorActionPreference = 'Stop'
Add-Type -AssemblyName System.Drawing
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$base = $PSScriptRoot
$dirImagenes = Join-Path $base 'imagenes'
$dirDocumentos = Join-Path $base 'documentos'
$dirNegativos = Join-Path $base 'negativos'

foreach ($d in @($dirImagenes, $dirDocumentos, $dirNegativos)) {
    if (-not (Test-Path -LiteralPath $d)) { New-Item -ItemType Directory -Path $d -Force | Out-Null }
}

$creados = @()

# ---------------------------------------------------------------- JPEG

# En este build de PowerShell el metodo estatico SolidBrush.FromArgb no esta
# expuesto, asi que el pincel se construye por constructor de instancia.
function Pincel {
    param([int]$R, [int]$G, [int]$B, [int]$A = 255)
    New-Object System.Drawing.SolidBrush ([System.Drawing.Color]::FromArgb($A, $R, $G, $B))
}

function Nueva-ImagenDocumento {
    param(
        [int]$Ancho = 850,
        [int]$Alto = 1100,
        [string]$Titulo = 'Documento de prueba',
        [int]$Semilla = 7
    )

    $rnd = [System.Random]::new($Semilla)
    $bmp = [System.Drawing.Bitmap]::new($Ancho, $Alto, [System.Drawing.Imaging.PixelFormat]::Format24bppRgb)
    $g = [System.Drawing.Graphics]::FromImage($bmp)
    $g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::None
    $g.Clear([System.Drawing.Color]::White)

    # Marco
    $lapizBorde = [System.Drawing.Pen]::new([System.Drawing.Color]::FromArgb(90, 107, 122), 4)
    $g.DrawRectangle($lapizBorde, 2, 2, $Ancho - 5, $Alto - 5)

    # Encabezado institucional
    $fuenteTitulo = [System.Drawing.Font]::new('Arial', 15, [System.Drawing.FontStyle]::Bold)
    $fuenteCorta = [System.Drawing.Font]::new('Arial', 9, [System.Drawing.FontStyle]::Italic)
    $fuenteCuerpo = [System.Drawing.Font]::new('Arial', 10, [System.Drawing.FontStyle]::Regular)
    $tinta = Pincel 31 58 95
    $gris = [System.Drawing.Brushes]::DimGray

    $g.DrawString('UNIVERSIDAD PRIVADA DE SANTA CRUZ DE LA SIERRA', $fuenteTitulo, $tinta, 60, 32)
    $g.DrawString('Sede Tarija - Bolivia', $fuenteCorta, $gris, 60, 60)
    $g.FillRectangle((Pincel 31 58 95), 60, 82, $Ancho - 180, 2)
    $g.DrawString($Titulo, $fuenteTitulo, $tinta, 60, 96)

    # Barras que simulan texto impreso
    $y = [int]($Alto * 0.22)
    for ($i = 0; $i -lt 8; $i++) {
        $largo = [int](($Ancho - 200) * (0.92 - ($rnd.NextDouble() * 0.28)))
        $g.FillRectangle((Pincel 58 58 58), 70, $y, $largo, 5)
        $y += 24
    }

    # Firmas al pie
    $yFirma = $Alto - 210
    $g.FillRectangle([System.Drawing.Brushes]::DimGray, 70, $yFirma, 230, 3)
    $g.DrawString('Docente tutor', $fuenteCorta, $gris, 70, $yFirma + 8)
    $g.FillRectangle([System.Drawing.Brushes]::DimGray, $Ancho - 330, $yFirma, 230, 3)
    $g.DrawString('Coordinacion', $fuenteCorta, $gris, $Ancho - 330, $yFirma + 8)

    # Sello
    $sello = Pincel 0 102 153 90
    $g.FillEllipse($sello, $Ancho - 250, $Alto - 340, 130, 130)
    $g.DrawString('PRUEBA', $fuenteCorta, [System.Drawing.Brushes]::White, $Ancho - 222, $Alto - 285)

    $g.Dispose()
    return $bmp
}

$jpegs = @(
    @{ Archivo = 'evidencia_reunion_03.jpg'; Titulo = 'Evidencia de reunion No. 3'; Semilla = 11 },
    @{ Archivo = 'comprobante_pago_03.jpg'; Titulo = 'Comprobante de pago MG'; Semilla = 23 },
    @{ Archivo = 'expediente_fotografia_carnet.jpg'; Titulo = 'Fotografia del carnet de estudiante'; Semilla = 37 }
)

foreach ($j in $jpegs) {
    $ruta = Join-Path $dirImagenes $j.Archivo
    $bmp = Nueva-ImagenDocumento -Titulo $j.Titulo -Semilla $j.Semilla
    $bmp.Save($ruta, [System.Drawing.Imaging.ImageFormat]::Jpeg)
    $bmp.Dispose()
    $creados += @{ Archivo = "imagenes/$($j.Archivo)"; Bytes = (Get-Item -LiteralPath $ruta).Length }
}

# ---------------------------------------------------------------- DOCX

function Comprimir-Docx {
    param(
        [string]$RutaDestino,
        [string]$Titulo,
        [string]$Subtitulo,
        [string[]]$Parrafos
    )

    $relaciones = @{
        '[Content_Types].xml' = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
<Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
'@
        '_rels/.rels' = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
'@
        'docProps/core.xml' = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<dc:title>$Titulo</dc:title>
<dc:creator>Sistema de Tutorias Academicas UPDS</dc:creator>
<cp:lastModifiedBy>generador_binarios.ps1</cp:lastModifiedBy>
</cp:coreProperties>
"@
        'docProps/app.xml' = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
<Application>generador_binarios.ps1</Application>
</Properties>
'@
        'word/styles.xml' = @'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styles xmlns="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/></style>
<style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:rPr><w:b/><w:sz w:val="32"/></w:rPr></style>
<style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:rPr><w:b/><w:sz w:val="26"/></w:rPr></style>
</styles>
'@
    }

    function Esc { param($t) [System.Security.SecurityElement]::Escape($t) }

    $cuerpo = "<w:p><w:pPr><w:pStyle w:val=`"Title`"/></w:pPr><w:r><w:t xml:space=`"preserve`">$(Esc $Titulo)</w:t></w:r></w:p>"
    $cuerpo += "<w:p><w:r><w:rPr><w:i/></w:rPr><w:t xml:space=`"preserve`">$(Esc $Subtitulo)</w:t></w:r></w:p>"
    $cuerpo += "<w:p/>"
    foreach ($p in $Parrafos) {
        $cuerpo += "<w:p><w:pPr><w:pStyle w:val=`"Heading1`"/></w:pPr><w:r><w:t xml:space=`"preserve`">$(Esc $p)</w:t></w:r></w:p>"
    }

    $relaciones['word/document.xml'] = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
<w:body>
$cuerpo
<w:sectPr><w:pgSz w:w="11906" w:h="16838"/></w:sectPr>
</w:body>
</w:document>
"@

    if (Test-Path -LiteralPath $RutaDestino) { Remove-Item -LiteralPath $RutaDestino -Force }
    $fs = [System.IO.File]::Open($RutaDestino, [System.IO.FileMode]::CreateNew)
    try {
        $zip = [System.IO.Compression.ZipArchive]::new($fs, [System.IO.Compression.ZipArchiveMode]::Create)
        try {
            foreach ($nombre in @('[Content_Types].xml', '_rels/.rels', 'docProps/core.xml', 'docProps/app.xml', 'word/styles.xml', 'word/document.xml')) {
                $entrada = $zip.CreateEntry($nombre, [System.IO.Compression.CompressionLevel]::Optimal)
                $escritor = [System.IO.StreamWriter]::new($entrada.Open(), (New-Object System.Text.UTF8Encoding($false)))
                $escritor.Write($relaciones[$nombre])
                $escritor.Dispose()
            }
        } finally { $zip.Dispose() }
    } finally { $fs.Dispose() }
}

$docx = @(
    @{
        Archivo    = 'expediente_curriculum_vitae.docx'
        Titulo     = 'Curriculum Vitae'
        Subtitulo  = 'Maria Fernanda Quisbert Antezana - Registro Universitario 71234567'
        Parrafos   = @('Formacion academica', 'Ingenieria de Sistemas, UPDS Sede Tarija', 'Perfil profesional', 'Estudiante de ultimo semestre con interes en ingenieria de software', 'Actividades', 'Participacion en proyecto de investigacion academica 2025')
    },
    @{
        Archivo    = 'expediente_ensayo_tesis.docx'
        Titulo     = 'Ensayo de Tesis'
        Subtitulo  = 'Anteproyecto - Sistema de gestion de tutorias academicas'
        Parrafos   = @('Resumen', 'El presente ensayo propone una plataforma de gestion de tutorias', 'Palabras clave', 'tutoria, gestion academica, PHP, MySQL', 'Introduccion', 'La tutoria academica cumple un rol de soporte al proceso de graduacion')
    }
)

foreach ($d in $docx) {
    $ruta = Join-Path $dirDocumentos $d.Archivo
    Comprimir-Docx -RutaDestino $ruta -Titulo $d.Titulo -Subtitulo $d.Subtitulo -Parrafos $d.Parrafos
    $creados += @{ Archivo = "documentos/$($d.Archivo)"; Bytes = (Get-Item -LiteralPath $ruta).Length }
}

# DOCX renombrado a PDF: debe rechazarse por Discordancia de MIME.
$rutaMascarado = Join-Path $dirNegativos 'docx_renombrado_como_pdf.pdf'
Copy-Item -LiteralPath (Join-Path $dirDocumentos 'expediente_ensayo_tesis.docx') -Destination $rutaMascarado -Force
$creados += @{ Archivo = "negativos/docx_renombrado_como_pdf.pdf"; Bytes = (Get-Item -LiteralPath $rutaMascarado).Length }

# ---------------------------------------------------------------- salida

Write-Output "ARCHIVOS BINARIOS GENERADOS"
Write-Output ('-' * 62)
foreach ($c in $creados) {
    '{0,-46} {1,10} bytes' -f $c.Archivo, $c.Bytes
}
Write-Output ('-' * 62)
Write-Output ("Total: {0} archivos" -f $creados.Count)
