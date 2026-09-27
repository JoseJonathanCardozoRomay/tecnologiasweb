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

        $stmt->execute([
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':correo'   => $datos['correo'],
            ':usuario'  => $datos['usuario'],
            ':hash'     => $hash,
        ]);

        return (int) $this->pdo->lastInsertId();
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

    public function actualizarPerfilPropio($id_usuario, $datos)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE usuarios
             SET nombre = :nombre, apellido = :apellido, correo = :correo, telefono = :telefono
             WHERE id_usuario = :id_usuario"
        );

        return $stmt->execute([
            ':nombre'     => $datos['nombre'],
            ':apellido'   => $datos['apellido'],
            ':correo'     => $datos['correo'],
            ':telefono'   => $datos['telefono'] !== '' ? $datos['telefono'] : null,
            ':id_usuario' => $id_usuario,
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

    public function obtenerEstudiantesConPerfil()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, e.id_estudiante,
                       c.nombre_carrera, e.registro_universitario
                FROM usuarios u
                INNER JOIN estudiantes e ON u.id_usuario = e.id_usuario
                INNER JOIN carreras c ON e.id_carrera = c.id_carrera
                WHERE u.estado = 'activo'
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTutoresConPerfil()
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, t.id_tutor,
                       t.especialidad
                FROM usuarios u
                INNER JOIN tutores t ON u.id_usuario = t.id_usuario
                WHERE u.estado = 'activo'
                ORDER BY u.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * SPRINT 6 (UX/seguridad): roles disponibles directamente de la BD.
     */
    public function obtenerRoles()
    {
        return $this->pdo->query("SELECT id_rol, nombre_rol FROM roles ORDER BY id_rol")->fetchAll();
    }

    /**
     * Estados de cuenta (activo/inactivo) presentes en la BD (dinámicos).
     */
    public function estados()
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT estado FROM usuarios WHERE estado <> '' ORDER BY estado");
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'estado');
    }

    /**
     * Autocompletado en tiempo real por nombre real, apellido o username.
     * Preparado con LIKE enlazado (sin concatenar entradas del usuario).
     */
    public function buscarActivos(string $texto, int $limite = 10)
    {
        $busqueda = '%' . escapaLike(trim($texto)) . '%';
        $stmt = $this->pdo->prepare(
            "SELECT id_usuario, usuario, nombre, apellido, correo
             FROM usuarios
             WHERE estado = 'activo'
               AND (nombre LIKE :busq_nombre OR apellido LIKE :busq_apellido
                    OR usuario LIKE :busq_usuario OR correo LIKE :busq_correo)
             ORDER BY nombre ASC, apellido ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':busq_nombre', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busq_apellido', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busq_usuario', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':busq_correo', $busqueda, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}