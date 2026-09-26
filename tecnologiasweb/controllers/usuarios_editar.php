<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../includes/sesion.php';

// Solamente el administrador puede modificar usuarios
requerirRol(
    ['administrador'],
    '../index.php'
);

$modeloUsuario = new UsuarioModel($pdo);

// El identificador puede llegar por GET o desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idUsuario = filter_input(
        INPUT_POST,
        'id_usuario',
        FILTER_VALIDATE_INT
    );
} else {
    $idUsuario = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );
}

if (!$idUsuario) {
    header(
        'Location: usuarios_listar.php?estado=no_encontrado'
    );
    exit;
}

$usuarioEncontrado = $modeloUsuario->buscarPorId($idUsuario);

if (!$usuarioEncontrado) {
    header(
        'Location: usuarios_listar.php?estado=no_encontrado'
    );
    exit;
}

$roles = $modeloUsuario->listarRoles();

$nombre = $usuarioEncontrado['nombre'];
$apellido = $usuarioEncontrado['apellido'];
$correo = $usuarioEncontrado['correo'];
$usuario = $usuarioEncontrado['usuario'];
$telefono = $usuarioEncontrado['telefono'] ?? '';
$idRol = (int) $usuarioEncontrado['id_rol'];
$estadoUsuario = $usuarioEncontrado['estado'];

$usuarioSesion = obtenerUsuarioSesion();

$esUsuarioActual = (int) $usuarioSesion['id_usuario']
    === $idUsuario;

$error = '';

// Procesamos los cambios enviados desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $confirmarContrasena = $_POST['confirmar_contrasena'] ?? '';

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
        $contrasena !== ''
        && strlen($contrasena) < 8
    ) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($contrasena !== $confirmarContrasena) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (
        $modeloUsuario->existeUsuario(
            $usuario,
            $idUsuario
        )
    ) {
        $error = 'El nombre de usuario ya está registrado.';
    } elseif (
        $modeloUsuario->existeCorreo(
            $correo,
            $idUsuario
        )
    ) {
        $error = 'El correo electrónico ya está registrado.';
    } else {
        try {
            $pdo->beginTransaction();

            $modeloUsuario->actualizar(
                $idUsuario,
                $idRol,
                $nombre,
                $apellido,
                $correo,
                $usuario,
                $telefono !== '' ? $telefono : null,
                $estadoUsuario
            );

            // La contraseña cambia solamente si se escribió una nueva
            if ($contrasena !== '') {
                $contrasenaHash = password_hash(
                    $contrasena,
                    PASSWORD_DEFAULT
                );

                $modeloUsuario->actualizarContrasena(
                    $idUsuario,
                    $contrasenaHash
                );
            }

            $pdo->commit();

            // Actualizamos los datos visibles de la sesión actual
            if ($esUsuarioActual) {
                $_SESSION['usuario']['nombre'] = $nombre;
                $_SESSION['usuario']['apellido'] = $apellido;
                $_SESSION['usuario']['usuario'] = $usuario;
            }

            header(
                'Location: usuarios_listar.php?estado=actualizado'
            );
            exit;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = 'No fue posible actualizar el usuario.';
        }
    }
}

// Buscamos el nombre del rol para mostrarlo en la vista
$nombreRol = '';

foreach ($roles as $rol) {
    if ((int) $rol['id_rol'] === $idRol) {
        $nombreRol = $rol['nombre_rol'];
        break;
    }
}

// Datos utilizados por la vista
$tituloPagina = 'Editar usuario';
$rutaBase = '../';

require_once __DIR__ . '/../views/usuarios/editar.php';