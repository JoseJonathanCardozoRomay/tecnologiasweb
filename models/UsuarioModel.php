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

    public function obtenerTodosOrdenados($orden, $dir)
    {
        $columnas = [
            'id' => 'u.id_usuario',
            'nombre' => 'u.nombre',
            'correo' => 'u.correo',
            'rol' => 'r.nombre_rol',
            'estado' => 'u.estado',
            'fecha' => 'u.fecha_registro',
        ];
        $orden = $columnas[$orden] ?? $columnas['id'];
        $dir = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.usuario,
                       r.nombre_rol, u.estado, u.fecha_registro
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                ORDER BY {$orden} {$dir}";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPaginadas($q, $orden, $dir, $limite, $offset, $estado = '')
    {
        $columnas = ['id' => 'u.id_usuario', 'nombre' => 'u.nombre', 'correo' => 'u.correo', 'rol' => 'r.nombre_rol', 'estado' => 'u.estado', 'fecha' => 'u.fecha_registro'];
        $ordenSql = $columnas[$orden] ?? $columnas['id'];
        $dirSql = strtolower($dir) === 'asc' ? 'ASC' : 'DESC';
        $sql = "SELECT u.id_usuario, u.nombre, u.apellido, u.correo, u.usuario, r.nombre_rol, u.estado, u.fecha_registro
                FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol";
        $params = [];
        $condiciones = [];
        if ($estado !== '') $condiciones[] = 'u.estado = :estado';
        if ($q !== '') {
            $condiciones[] = "CONCAT_WS(' ', u.nombre, u.apellido, u.usuario, u.correo, r.nombre_rol) LIKE :q ESCAPE '\\\\'";
            $params[':q'] = valorBusquedaLike($q);
        }
        if ($condiciones) $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        $sql .= " ORDER BY {$ordenSql} {$dirSql} LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        if ($q !== '') $stmt->bindValue(':q', $params[':q'], PDO::PARAM_STR);
        if ($estado !== '') $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($q = '', $estado = '')
    {
        $sql = 'SELECT COUNT(*) FROM usuarios u INNER JOIN roles r ON u.id_rol = r.id_rol';
        $condiciones = [];
        if ($estado !== '') $condiciones[] = 'u.estado = :estado';
        if ($q !== '') $condiciones[] = "CONCAT_WS(' ', u.nombre, u.apellido, u.usuario, u.correo, r.nombre_rol) LIKE :q ESCAPE '\\\\'";
        if ($condiciones) $sql .= ' WHERE ' . implode(' AND ', $condiciones);
        $stmt = $this->pdo->prepare($sql);
        if ($q !== '') $stmt->bindValue(':q', valorBusquedaLike($q), PDO::PARAM_STR);
        if ($estado !== '') $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeCorreo($correo, $excluirId = null)
    {
        return $this->existeCampo('correo', $correo, $excluirId);
    }

    public function existeUsuario($usuario, $excluirId = null)
    {
        return $this->existeCampo('usuario', $usuario, $excluirId);
    }

    private function existeCampo($campo, $valor, $excluirId)
    {
        if (!in_array($campo, ['correo', 'usuario'], true)) {
            return false;
        }
        $sql = "SELECT 1 FROM usuarios WHERE {$campo} = :valor";
        $params = [':valor' => $valor];
        if ($excluirId !== null) {
            $sql .= ' AND id_usuario <> :excluir_id';
            $params[':excluir_id'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    public function crear($datos)
    {
        // password_hash genera un hash seguro (bcrypt) — NUNCA guardar la contraseña en texto plano
        $hash = password_hash($datos['clave'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (id_rol, nombre, apellido, correo, usuario, contrasena_hash, telefono)
            VALUES (:id_rol, :nombre, :apellido, :correo, :usuario, :hash, :telefono)";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':correo'   => $datos['correo'],
            ':usuario'  => $datos['usuario'],
            ':hash'     => $hash,
            ':telefono' => $datos['telefono'] ?? null,
        ]);
    }

    public function actualizar($id, $datos)
    {
        $sql = "UPDATE usuarios
                SET id_rol = :id_rol, nombre = :nombre, apellido = :apellido,
                    correo = :correo, usuario = :usuario, telefono = :telefono, estado = :estado";
        $params = [
            ':id_rol'   => $datos['id_rol'],
            ':nombre'   => $datos['nombre'],
            ':apellido' => $datos['apellido'],
            ':correo'   => $datos['correo'],
            ':usuario'  => $datos['usuario'],
            ':telefono' => $datos['telefono'] ?? null,
            ':estado'   => $datos['estado'],
            ':id'       => $id,
        ];
        if (!empty($datos['clave'])) {
            $sql = str_replace('estado = :estado', 'estado = :estado, contrasena_hash = :hash', $sql);
            $params[':hash'] = password_hash($datos['clave'], PASSWORD_DEFAULT);
        }
        $sql .= ' WHERE id_usuario = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
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

    public function obtenerIdRol($nombreRol)
    {
        $stmt = $this->pdo->prepare('SELECT id_rol FROM roles WHERE nombre_rol = :rol LIMIT 1');
        $stmt->execute([':rol' => $nombreRol]);
        return $stmt->fetchColumn();
    }

    public function actualizarEstado($id, $estado)
    {
        $stmt = $this->pdo->prepare('UPDATE usuarios SET estado = :estado WHERE id_usuario = :id');
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}