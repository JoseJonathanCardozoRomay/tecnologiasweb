<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ExpedienteMgModel.php';
require_once __DIR__ . '/../models/EstudianteModel.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../models/CohorteMgModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg',
        'auxiliar_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.expedientes.crear',
    '../index.php'
);

$modeloExpediente = new ExpedienteMgModel($pdo);
$modeloEstudiante = new EstudianteModel($pdo);
$modeloModalidad = new ModalidadModel($pdo);
$modeloCohorte = new CohorteMgModel($pdo);

$usuarioSesion = obtenerUsuarioSesion();

// Solo mostramos estudiantes con cuentas activas
$estudiantes = array_values(
    array_filter(
        $modeloEstudiante->listar(),
        static function (
            array $estudiante
        ): bool {
            return $estudiante['estado'] === 'activo';
        }
    )
);

$modalidades = $modeloModalidad->listarActivas();
$cohortes = $modeloCohorte->listarActivas();

$idEstudiante = null;
$idModalidad = null;
$idCohorte = null;
$tituloTrabajo = '';
$fechaInicio = '';
$observaciones = '';
$error = '';

// Comprueba que una fecha tenga el formato esperado
$esFechaValida = static function (
    string $fecha
): bool {
    $fechaConvertida = DateTime::createFromFormat(
        '!Y-m-d',
        $fecha
    );

    return $fechaConvertida !== false
        && $fechaConvertida->format('Y-m-d') === $fecha;
};

// Procesamos el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEstudiante = filter_input(
        INPUT_POST,
        'id_estudiante',
        FILTER_VALIDATE_INT
    );

    $idModalidad = filter_input(
        INPUT_POST,
        'id_modalidad',
        FILTER_VALIDATE_INT
    );

    $idCohorte = filter_input(
        INPUT_POST,
        'id_cohorte',
        FILTER_VALIDATE_INT
    );

    $tituloRecibido = $_POST['titulo_trabajo'] ?? '';
    $fechaRecibida = $_POST['fecha_inicio'] ?? '';
    $observacionesRecibidas = $_POST['observaciones'] ?? '';

    $tituloTrabajo = is_string($tituloRecibido)
        ? trim($tituloRecibido)
        : '';

    $fechaInicio = is_string($fechaRecibida)
        ? trim($fechaRecibida)
        : '';

    $observaciones = is_string($observacionesRecibidas)
        ? trim($observacionesRecibidas)
        : '';

    $estudiante = $idEstudiante
        ? $modeloEstudiante->buscarPorId($idEstudiante)
        : null;

    $modalidad = $idModalidad
        ? $modeloModalidad->buscarPorId($idModalidad)
        : null;

    $cohorte = $idCohorte
        ? $modeloCohorte->buscarPorId($idCohorte)
        : null;

    $idUsuario = (int) (
        $usuarioSesion['id_usuario']
        ?? 0
    );

    if (!validarTokenCsrf()) {
        http_response_code(403);

        $error = 'La solicitud no superó la validación de seguridad.';
    } elseif (
        !$estudiante
        || $estudiante['estado'] !== 'activo'
    ) {
        $error = 'Selecciona un estudiante activo.';
    } elseif (
        !$modalidad
        || (int) $modalidad['activa'] !== 1
    ) {
        $error = 'Selecciona una modalidad activa.';
    } elseif (
        !$cohorte
        || (int) $cohorte['activa'] !== 1
    ) {
        $error = 'Selecciona una cohorte activa.';
    } elseif (!$esFechaValida($fechaInicio)) {
        $error = 'Selecciona una fecha de inicio válida.';
    } elseif (
        $fechaInicio < $cohorte['fecha_inicio']
    ) {
        $error = 'La fecha de inicio no puede ser anterior al comienzo de la cohorte.';
    } elseif (
        !empty($cohorte['fecha_fin'])
        && $fechaInicio > $cohorte['fecha_fin']
    ) {
        $error = 'La fecha de inicio no puede ser posterior al cierre de la cohorte.';
    } elseif (mb_strlen($tituloTrabajo) > 200) {
        $error = 'El título del trabajo no puede superar los 200 caracteres.';
    } elseif (mb_strlen($observaciones) > 2000) {
        $error = 'Las observaciones no pueden superar los 2000 caracteres.';
    } elseif ($idUsuario <= 0) {
        $error = 'No fue posible identificar al usuario actual.';
    } elseif (
        $modeloExpediente->existeProceso(
            $idEstudiante,
            $idModalidad,
            $idCohorte
        )
    ) {
        $error = 'El estudiante ya tiene esta modalidad registrada dentro de la cohorte.';
    } else {
        try {
            $modeloExpediente->crear(
                $idEstudiante,
                $idModalidad,
                $idCohorte,
                $tituloTrabajo !== ''
                    ? $tituloTrabajo
                    : null,
                $fechaInicio,
                $observaciones !== ''
                    ? $observaciones
                    : null,
                $idUsuario
            );

            header(
                'Location: expedientes_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'El estudiante ya tiene este proceso registrado.';
            } else {
                $error = 'No fue posible registrar el expediente.';
            }
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo expediente';
$rutaBase = '../';

require_once __DIR__
    . '/../views/expedientes/crear.php';