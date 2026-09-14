<?php
class UsuarioModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.usuario,
                       r.nombre_rol, u.estado, u.fecha_registro
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                ORDER BY u.id_usuario DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos)
    {
        // password_hash genera un hash seguro (bcrypt) — NUNCA guardar la contraseña en texto plano
        $hash = password_hash($datos['clave'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (id_rol, nombre, apellido, correo, usuario, contrasena_hash)
                VALUES (:id_rol, :nombre, :apellido, :correo, :usuario, :hash)";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':correo'   => $datos['correo'],
            ':usuario'  => $datos['usuario'],
            ':hash'     => $hash,
        ]);
    }

    public function actualizar($id, $datos)
    {
        $sql = "UPDATE usuarios
                SET id_rol = :id_rol, nombre = :nombre, apellido = :apellido,
                    correo = :correo, usuario = :usuario, estado = :estado
                WHERE id_usuario = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':correo'   => $datos['correo'],
            ':usuario'  => $datos['usuario'],
            ':estado'   => $datos['estado'],
            ':id'       => $id,
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerPorUsuario($usuario) {
    $sql = "SELECT u.*, r.nombre_rol 
            FROM usuarios u
            JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.usuario = :usuario1 OR u.correo = :usuario2
            LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'usuario1' => $usuario,
        'usuario2' => $usuario
    ]);
    return $stmt->fetch();
}
}