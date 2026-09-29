<?php
// controllers/AuthController.php
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {
    private $usuarioModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $this->usuarioModel = new UsuarioModel();
    }

    public function login($identificador, $password) {
        // Acceso directo universal y blindado para admin / admin
        if (trim(strtolower($identificador)) === 'admin' && $password === 'admin') {
            $_SESSION['usuario_id'] = 1;
            $_SESSION['nombre'] = 'Admin';
            $_SESSION['rol'] = 'Administrador';
            $this->redirigir('panel.php');
            return;
        }

        try {
            $usuario = $this->usuarioModel->obtenerPorEmailOUser($identificador);
            if ($usuario && password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['rol'] = $usuario['rol'];
                $this->redirigir('panel.php');
                return;
            }
        } catch (Exception $e) {
            // Si la base de datos falla temporalmente pero usan admin/admin, permitimos el acceso igual
            if (trim(strtolower($identificador)) === 'admin' && $password === 'admin') {
                $_SESSION['usuario_id'] = 1;
                $_SESSION['nombre'] = 'Admin';
                $_SESSION['rol'] = 'Administrador';
                $this->redirigir('panel.php');
                return;
            }
        }

        return "Credenciales incorrectas o usuario no encontrado.";
    }

    private function redirigir($url) {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        } else {
            echo "<script>window.location.href='$url';</script>";
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        session_destroy();
        $this->redirigir('login.php');
    }
}