<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__
    . '/../models/RegistroAccesoModel.php';
require_once __DIR__ . '/../includes/sesion.php';
require_once __DIR__ . '/../includes/csrf.php';

$modeloUsuario = new UsuarioModel($pdo);
$modeloAcceso = new RegistroAccesoModel($pdo);

// Si el usuario ya ingresó, lo enviamos a la página principal
if (usuarioAutenticado()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$usuarioIngresado = '';

// Valores utilizados por el bloqueo temporal
$maximoIntentos = 5;
$minutosBloqueo = 15;

// Hash utilizado para igualar el tiempo de respuesta cuando la cuenta
// escrita no existe.
$hashComparacion = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

// Procesamos los datos solamente cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuarioIngresado = trim(
        $_POST['usuario'] ?? ''
    );

    $contrasena = $_POST['contrasena'] ?? '';

    $ipOrigen = mb_substr(
        $_SERVER['REMOTE_ADDR'] ?? 'desconocida',
        0,
        45
    );

    if (!validarTokenCsrf()) {
        $error = 'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.';
    } elseif (
        $usuarioIngresado === ''
        || $contrasena === ''
    ) {
        $error = 'Completa el usuario y la contraseña.';
    } elseif (
        mb_strlen($usuarioIngresado) > 50
        || mb_strlen($contrasena) > 255
    ) {
        $error = 'El usuario o la contraseña son incorrectos.';
    } else {
        try {
            $usuarioEncontrado =
                $modeloUsuario->buscarPorUsuario(
                    $usuarioIngresado
                );

            $idUsuario = $usuarioEncontrado
                ? (int) $usuarioEncontrado['id_usuario']
                : null;

            if (
                $modeloAcceso->estaBloqueado(
                    $ipOrigen,
                    $idUsuario,
                    $maximoIntentos,
                    $minutosBloqueo
                )
            ) {
                $error = 'Se realizaron demasiados intentos. Espera 15 minutos antes de volver a intentarlo.';
            } else {
                /*
                 * Siempre verificamos un hash, incluso cuando la cuenta
                 * no existe. Esto reduce diferencias de tiempo que
                 * podrían utilizarse para descubrir usuarios válidos.
                 */
                $hashVerificar = $usuarioEncontrado
                    ? $usuarioEncontrado['contrasena_hash']
                    : $hashComparacion;

                $contrasenaValida = password_verify(
                    $contrasena,
                    $hashVerificar
                );

                $accesoValido =
                    $usuarioEncontrado
                    && $usuarioEncontrado['estado'] === 'activo'
                    && $contrasenaValida;

                if (!$accesoValido) {
                    $modeloAcceso->registrar(
                        $idUsuario,
                        'fallido',
                        $ipOrigen
                    );

                    if (
                        $modeloAcceso->estaBloqueado(
                            $ipOrigen,
                            $idUsuario,
                            $maximoIntentos,
                            $minutosBloqueo
                        )
                    ) {
                        $error = 'Se realizaron demasiados intentos. Espera 15 minutos antes de volver a intentarlo.';
                    } else {
                        // No indicamos si falló el usuario, la clave
                        // o si la cuenta se encuentra inactiva.
                        $error = 'El usuario o la contraseña son incorrectos.';
                    }
                } else {
                    // Renovamos el identificador al autenticar
                    session_regenerate_id(true);

                    $ahora = time();

                    $_SESSION['usuario'] = [
                        'id_usuario' => $idUsuario,
                        'nombre' => $usuarioEncontrado['nombre'],
                        'apellido' => $usuarioEncontrado['apellido'],
                        'usuario' => $usuarioEncontrado['usuario'],
                        'rol' => $usuarioEncontrado['nombre_rol']
                    ];

                    $_SESSION['inicio_sesion'] = $ahora;
                    $_SESSION['ultima_actividad'] = $ahora;
                    $_SESSION['ultima_renovacion'] = $ahora;

                    // El token anterior deja de ser válido
                    eliminarTokenCsrf();

                    $modeloAcceso->registrar(
                        $idUsuario,
                        'exitoso',
                        $ipOrigen
                    );

                    header('Location: ../index.php');
                    exit;
                }
            }
        } catch (PDOException $e) {
            error_log(
                'Error durante el inicio de sesión: '
                . $e->getMessage()
            );

            $error = 'No fue posible iniciar sesión en este momento.';
        }
    }
}

// Estos datos serán utilizados por la vista del formulario
$tituloPagina = 'Iniciar sesión';
$rutaBase = '../';

require_once __DIR__ . '/../views/auth/login.php';