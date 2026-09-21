<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede registrar usuarios
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloUsuario = new UsuarioModel($pdo);
$roles = $modeloUsuario->listarRoles();

$nombre = '';
$apellido = '';
$correo = '';
$usuario = '';
$telefono = '';
$idRol = null;
$error = '';

// Procesamos los datos enviados por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

    $idRol = filter_input(
        INPUT_POST,
        'id_rol',
        FILTER_VALIDATE_INT
    );

    $rolesValidos = array_map(
        fn(array $rol): int => (int) $rol['id_rol'],
        $roles
    );

    if ($nombre === '' || $apellido === '') {
        $error = 'Completa el nombre y el apellido.';
    } elseif (
        mb_strlen($nombre) > 100
        || mb_strlen($apellido) > 100
    ) {
        $error = 'El nombre y el apellido no pueden superar los 100 caracteres.';
    } elseif (
        !filter_var($correo, FILTER_VALIDATE_EMAIL)
    ) {
        $error = 'Ingresa un correo electrónico válido.';
    } elseif (mb_strlen($correo) > 150) {
        $error = 'El correo no puede superar los 150 caracteres.';
    } elseif (
        mb_strlen($usuario) < 4
        || mb_strlen($usuario) > 50
    ) {
        $error = 'El usuario debe tener entre 4 y 50 caracteres.';
    } elseif (
        !preg_match('/^[a-zA-Z0-9._-]+$/', $usuario)
    ) {
        $error = 'El usuario solo puede contener letras, números, puntos, guiones y guion bajo.';
    } elseif (
        $telefono !== ''
        && !preg_match('/^[0-9+\-\s]{7,20}$/', $telefono)
    ) {
        $error = 'Ingresa un número de teléfono válido.';
    } elseif (
        !$idRol
        || !in_array($idRol, $rolesValidos, true)
    ) {
        $error = 'Selecciona un rol válido.';
    } elseif (strlen($contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } elseif ($contrasena !== $confirmarContrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif ($modeloUsuario->existeUsuario($usuario)) {
        $error = 'El nombre de usuario ya está registrado.';
    } elseif ($modeloUsuario->existeCorreo($correo)) {
        $error = 'El correo electrónico ya está registrado.';
    } else {
        try {
            $contrasenaHash = password_hash(
                $contrasena,
                PASSWORD_DEFAULT
            );

            $modeloUsuario->crear(
                $idRol,
                $nombre,
                $apellido,
                $correo,
                $usuario,
                $contrasenaHash,
                $telefono !== '' ? $telefono : null
            );

            header(
                'Location: usuarios_listar.php?estado=creado'
            );
            exit;
        } catch (PDOException $e) {
            $error = 'No fue posible registrar el usuario.';
        }
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Nuevo usuario';
$rutaBase = '../';

require_once __DIR__ . '/../views/usuarios/crear.php';