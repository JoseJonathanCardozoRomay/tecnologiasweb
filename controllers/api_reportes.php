<?php
/**
 * API de datos para los gráficos del módulo de reportes (Chart.js).
 * Solo accesible para administradores.
 *
 * Uso:
 *   GET /controllers/api_reportes.php?tipo=estados[&periodo=I-2026]
 *   GET /controllers/api_reportes.php?tipo=tutores[&periodo=I-2026]
 *   GET /controllers/api_reportes.php?tipo=materias[&periodo=I-2026]
 *   GET /controllers/api_reportes.php?tipo=mensual[&periodo=I-2026]
 *
 * Respuesta: JSON con etiquetas y valores listos para Chart.js.
 */

require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$tipo = $_GET['tipo'] ?? 'estados';

// El periodo es opcional; solo se acepta si coincide con un periodo existente.
$periodo = trim($_GET['periodo'] ?? '');
$tutoriaModel = new TutoriaModel($pdo);
if ($periodo !== '') {
    $periodosValidos = $tutoriaModel->obtenerPeriodosDisponibles();
    if (!in_array($periodo, $periodosValidos, true)) {
        $periodo = '';
    }
}
$periodoFiltro = $periodo !== '' ? $periodo : null;

try {
    switch ($tipo) {
        case 'estados':
            $filas = $tutoriaModel->obtenerConteoPorEstado($periodoFiltro);
            echo json_encode([
                'etiquetas' => array_column($filas, 'nombre'),
                'valores'   => array_column($filas, 'cantidad'),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'tutores':
            $filas = $tutoriaModel->obtenerConteoPorTutor($periodoFiltro);
            echo json_encode([
                'etiquetas' => array_column($filas, 'tutor'),
                'valores'   => array_column($filas, 'cantidad'),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'materias':
            $filas = $tutoriaModel->obtenerConteoPorMateria($periodoFiltro);
            echo json_encode([
                'etiquetas' => array_column($filas, 'materia'),
                'valores'   => array_column($filas, 'cantidad'),
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'mensual':
            $filas = $tutoriaModel->obtenerConteoPorMes($periodoFiltro);
            echo json_encode([
                'etiquetas' => array_column($filas, 'mes'),
                'valores'   => array_column($filas, 'cantidad'),
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de reporte no válido.'], JSON_UNESCAPED_UNICODE);
    }
} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'No se pudieron obtener los datos del reporte.'], JSON_UNESCAPED_UNICODE);
}
