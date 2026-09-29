<?php
// models/UsuarioModel.php
require_once __DIR__ . '/../config/conexion.php';

class UsuarioModel {
    private $pdo;

    public function __construct($pdo = null) {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function obtenerPorEmailOUser($identificador) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ? OR correo = ? OR nombre = ?");
        $stmt->execute([$identificador, $identificador, $identificador, $identificador]);
        return $stmt->fetch();
    }

    public function obtenerPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id = ? OR id_usuario = ?");
        $stmt->execute([$id, $id]);
        return $stmt->fetch();
    }

    public function obtenerTodos() {
        $stmt = $this->pdo->query("SELECT * FROM usuarios ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function crear($datos) {
        $hash = password_hash($datos['clave'], PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO usuarios (nombre, apellido, correo, usuario, password, rol, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'] ?? '',
            $datos['correo'],
            $datos['usuario'],
            $hash,
            $datos['id_rol'] ?? 1,
            $datos['estado'] ?? 'activo',
        ]);
    }

    public function actualizar($id, $datos) {
        $hash = !empty($datos['clave']) ? password_hash($datos['clave'], PASSWORD_BCRYPT) : null;

        if ($hash) {
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, usuario = ?, password = ?, id_rol = ?, estado = ?
                 WHERE id = ? OR id_usuario = ?"
            );
            return $stmt->execute([
                $datos['nombre'],
                $datos['apellido'] ?? '',
                $datos['correo'],
                $datos['usuario'],
                $hash,
                $datos['id_rol'] ?? 1,
                $datos['estado'] ?? 'activo',
                $id,
                $id,
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, usuario = ?, id_rol = ?, estado = ?
                 WHERE id = ? OR id_usuario = ?"
            );
            return $stmt->execute([
                $datos['nombre'],
                $datos['apellido'] ?? '',
                $datos['correo'],
                $datos['usuario'],
                $datos['id_rol'] ?? 1,
                $datos['estado'] ?? 'activo',
                $id,
                $id,
            ]);
        }
    }

    public function eliminar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ? OR id_usuario = ?");
        return $stmt->execute([$id, $id]);
    }
}
