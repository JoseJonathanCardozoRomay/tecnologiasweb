<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/MgPadronModel.php';
require_once __DIR__ . '/../models/MgCohorteModel.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';
require_once __DIR__ . '/../includes/lista_controlador.php';

$cohorteModel = new MgCohorteModel($pdo);
$cohortes = $cohorteModel->obtenerTodas();
$padronModel = new MgPadronModel($pdo);
$errores = [];
$resultado = null;

/** Normaliza una línea de cabecera a nombres canónicos de columna. */
function mgNormalizarCabecera(string $celda): string
{
    $celda = mb_strtolower(trim($celda));
    $celda = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $celda);
    $sin = preg_replace('/[^a-z0-9_]+/', '_', $celda);
    $sin = trim($sin, '_');

    $mapa = [
        'registro' => 'registro_universitario',
        'ru' => 'registro_universitario',
        'matricula' => 'registro_universitario',
        'registro_universitario' => 'registro_universitario',
        'apellido_paterno' => 'paterno',
        'paterno' => 'paterno',
        'apellidos_paterno' => 'paterno',
        'primer_apellido' => 'paterno',
        'apellido_materno' => 'materno',
        'materno' => 'materno',
        'segundo_apellido' => 'materno',
        'apellidos' => 'paterno',
        'apellido' => 'paterno',
        'nombres' => 'nombre',
        'nombre' => 'nombre',
        'primer_nombre' => 'nombre',
        'correo' => 'correo',
        'email' => 'correo',
        'mail' => 'correo',
        'correo_electronico' => 'correo',
        'carrera' => 'carrera',
        'codigo_carrera' => 'carrera',
        'id_carrera' => 'carrera',
        'semestre' => 'semestre',
        'semestre_actual' => 'semestre',
        'materias' => 'materias',
        'materias_completadas' => 'materias',
        'materias_aprobadas' => 'materias',
    ];

    return $mapa[$sin] ?? $sin;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validar();

    $idCohorte = (int) ($_POST['id_cohorte_mg'] ?? 0);
    if ($idCohorte <= 0 || !$cohorteModel->obtenerPorId($idCohorte)) {
        $errores[] = "Selecciona una cohorte válida.";
    }

    $archivo = $_FILES['archivo_csv'] ?? null;
    if (!$archivo || ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errores[] = "Selecciona un archivo CSV para importar.";
    } elseif (is_uploaded_file($archivo['tmp_name'])) {
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'txt'], true)) {
            $errores[] = "El archivo debe tener extensión .csv o .txt.";
        } elseif (!is_readable($archivo['tmp_name'])) {
            $errores[] = "No se pudo leer el archivo subido.";
        }
    }

    if (empty($errores)) {
        $ruta = $archivo['tmp_name'];
        $manejo = fopen($ruta, 'rb');
        $primeraLinea = fgets($manejo);
        rewind($manejo);
        $delimitador = (substr_count($primeraLinea, ';') >= substr_count($primeraLinea, ',')) ? ';' : ',';

        $cabeceras = fgetcsv($manejo, 0, $delimitador);
        if ($cabeceras === false) {
            $errores[] = "El archivo está vacío o no es un CSV válido.";
        } else {
            $columnas = array_map('mgNormalizarCabecera', $cabeceras);
            $requeridas = ['registro_universitario', 'nombre', 'correo', 'carrera'];
            $faltantes = array_diff($requeridas, $columnas);
            if ($faltantes) {
                $errores[] = "Faltan columnas requeridas: " . implode(', ', $faltantes)
                    . ". (Encabezados esperados: registro_universitario; paterno; materno; nombre; correo; carrera; semestre; materias)";
            } else {
                $filas = [];
                $nroFila = 2;
                while (($fila = fgetcsv($manejo, 0, $delimitador)) !== false) {
                    if (count($fila) === 1 && trim($fila[0] ?? '') === '') {
                        $nroFila++;
                        continue;
                    }
                    $registro = [];
                    foreach ($columnas as $i => $col) {
                        $registro[$col] = $fila[$i] ?? '';
                    }
                    $filas[] = $registro;
                    $nroFila++;
                }
                fclose($manejo);
                $resultado = $padronModel->importarFilas($filas, $idCohorte, (int) $_SESSION['id_usuario']);
                $resultado['cohorte'] = $cohorteModel->obtenerPorId($idCohorte)['nombre_periodo'] ?? '';
                $resultado['total_filas'] = count($filas);
            }
        }
        if (isset($manejo) && is_resource($manejo)) {
            fclose($manejo);
        }
    }
}

$cohorteSeleccionada = (int) ($_POST['id_cohorte_mg'] ?? $_GET['cohorte'] ?? 0);

require_once __DIR__ . '/../views/mg/padron/importar.php';