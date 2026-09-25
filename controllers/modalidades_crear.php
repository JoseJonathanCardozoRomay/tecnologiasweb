<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/ModalidadModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/permisos.php';
require_once __DIR__ . '/../includes/csrf.php';

requerirRol(
    [
        'administrador',
        'coordinador_mg'
    ],
    '../index.php'
);

requerirPermiso(
    'mg.modalidades.editar',
    '../index.php'
);

$modeloModalidad = new ModalidadModel($pdo);

$codigo = '';
$nombre = '';
$descripcion = '';
$requiereTutor = true;
$flujo = 'perfil_mg';
$error = '';

$flujosPermitidos = [
    'perfil_mg',
    'examen_areas',
    'excelencia'
];

// Procesamos la información enviada desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validarTokenCsrf()) {
        $error = 'La solicitud de seguridad no es válida o expiró.';
    } else {
        $codigoRecibido = $_POST['codigo'] ?? '';
        $nombreRecibido = $_POST['nombre'] ?? '';
        $descripcionRecibida = $_POST['descripcion'] ?? '';
        $flujoRecibido = $_POST['flujo'] ?? '';

        $codigo = is_string($codigoRecibido)
            ? strtoupper(trim($codigoRecibido))
            : '';

        $nombre = is_string($nombreRecibido)
            ? trim($nombreRecibido)
            : '';

        $descripcion = is_string($descripcionRecibida)
            ? trim($descripcionRecibida)
            : '';

        $flujo = is_string($flujoRecibido)
            ? trim($flujoRecibido)
            : '';

        $requiereTutor = (
            isset($_POST['requiere_tutor'])
            && $_POST['requiere_tutor'] === '1'
        );

        if (
            $codigo === ''
            || mb_strlen($codigo) < 2
            || mb_strlen($codigo) > 30
        ) {
            $error = 'El código debe contener entre 2 y 30 caracteres.';
        } elseif (
            !preg_match(
                '/^[A-Z0-9_]+$/',
                $codigo
            )
        ) {
            $error = 'El código solo puede contener letras mayúsculas, números y guiones bajos.';
        } elseif (
            $nombre === ''
            || mb_strlen($nombre) < 3
            || mb_strlen($nombre) > 100
        ) {
            $error = 'El nombre debe contener entre 3 y 100 caracteres.';
        } elseif (
            !preg_match(
                '/^[\p{L}\p{N}\s-]+$/u',
                $nombre
            )
        ) {
            $error = 'El nombre contiene caracteres no permitidos.';
        } elseif (
            mb_strlen($descripcion) > 255
        ) {
            $error = 'La descripción no puede superar los 255 caracteres.';
        } elseif (
            !in_array(
                $flujo,
                $flujosPermitidos,
                true
            )
        ) {
            $error = 'Selecciona un flujo válido.';
        } elseif (
            $requiereTutor
            && $flujo !== 'perfil_mg'
        ) {
            $error = 'Solamente el flujo de perfil MG puede requerir tutor.';
        } elseif (
            $modeloModalidad->existeCodigo($codigo)
        ) {
            $error = 'Ya existe una modalidad con ese código.';
        } elseif (
            $modeloModalidad->existeNombre($nombre)
        ) {
            $error = 'Ya existe una modalidad con ese nombre.';
        } else {
            try {
                $modeloModalidad->crear(
                    $codigo,
                    $nombre,
                    $descripcion !== ''
                        ? $descripcion
                        : null,
                    $requiereTutor,
                    $flujo
                );

                header(
                    'Location: modalidades_listar.php?estado=creado'
                );
                exit;
            } catch (PDOException $e) {
                $error = 'No fue posible registrar la modalidad.';
            }
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nueva modalidad';
$rutaBase = '../';

require_once __DIR__ . '/../views/modalidades/crear.php';