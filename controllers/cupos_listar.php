<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/CupoModel.php';
require_once __DIR__ . '/../models/PeriodoModel.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(['administrador'], '../index.php');

$modeloCupo = new CupoModel($pdo);
$modeloPeriodo = new PeriodoModel($pdo);

$idPeriodo = filter_input(INPUT_GET, 'periodo', FILTER_VALIDATE_INT);

if (!$idPeriodo || $idPeriodo < 1) {
    header('Location: periodos_listar.php?estado=no_encontrado');
    exit;
}

$periodo = $modeloPeriodo->buscarPorId($idPeriodo);
if (!$periodo) {
    header('Location: periodos_listar.php?estado=no_encontrado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estadoResultado = 'datos_invalidos';

    if (!validarTokenCsrf()) {
        $estadoResultado = 'token_invalido';
    } else {
        $idTutor = filter_input(
            INPUT_POST,
            'id_tutor',
            FILTER_VALIDATE_INT
        );
        $cupoMaximo = filter_input(
            INPUT_POST,
            'cupo_maximo',
            FILTER_VALIDATE_INT
        );
        $valorActivo = $_POST['activo'] ?? null;

        if (
            !$idTutor
            || $idTutor < 1
            || !$modeloCupo->existeTutor($idTutor)
        ) {
            $estadoResultado = 'tutor_no_encontrado';
        } elseif (
            $cupoMaximo === false
            || $cupoMaximo < 1
            || $cupoMaximo > 5
            || !in_array($valorActivo, ['0', '1'], true)
        ) {
            $estadoResultado = 'cupo_fuera_rango';
        } elseif (
            $cupoMaximo < $modeloCupo->contarOcupados(
                $idTutor,
                $idPeriodo
            )
        ) {
            $estadoResultado = 'cupo_inferior';
        } else {
            try {
                $guardado = $modeloCupo->guardar(
                    $idTutor,
                    $idPeriodo,
                    $cupoMaximo,
                    $valorActivo === '1'
                );
                $estadoResultado = $guardado
                    ? 'guardado'
                    : 'error';
            } catch (Throwable $error) {
                $estadoResultado = 'error';
            }
        }
    }

    header(
        'Location: cupos_listar.php?periodo='
        . $idPeriodo
        . '&estado='
        . rawurlencode($estadoResultado)
    );
    exit;
}

$busqueda = $_GET['buscar'] ?? '';
$busqueda = is_string($busqueda)
    ? mb_substr(trim($busqueda), 0, 100)
    : '';

$tutores = $modeloCupo->listarPorPeriodo($idPeriodo, $busqueda);
$resumen = $modeloCupo->obtenerResumen($idPeriodo);

$mensajes = [
    'guardado' => 'La configuración de cupos se guardó correctamente.',
    'datos_invalidos' => 'Revisa los datos enviados.',
    'token_invalido' => 'El formulario venció. Recarga la página e inténtalo otra vez.',
    'tutor_no_encontrado' => 'No se encontró el tutor seleccionado.',
    'cupo_fuera_rango' => 'El cupo debe estar entre 1 y 5 estudiantes.',
    'cupo_inferior' => 'El cupo no puede ser menor que los estudiantes ya inscritos.',
    'error' => 'No fue posible guardar la configuración.'
];

$estado = $_GET['estado'] ?? '';
$mensaje = $mensajes[$estado] ?? '';
$tipoMensaje = in_array(
    $estado,
    [
        'datos_invalidos',
        'token_invalido',
        'tutor_no_encontrado',
        'cupo_fuera_rango',
        'cupo_inferior',
        'error'
    ],
    true
) ? 'danger' : 'success';

$tituloPagina = 'Cupos por tutor';
$rutaBase = '../';

require_once __DIR__ . '/../views/cupos/listar.php';
