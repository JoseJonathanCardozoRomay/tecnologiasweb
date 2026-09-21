<?php

class UsuarioModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Buscamos al usuario para realizar el inicio de sesión
    public function buscarPorUsuario(string $usuario): ?array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.id_rol,
                u.nombre,
                u.apellido,
                u.usuario,
                u.contrasena_hash,
                u.estado,
                r.nombre_rol
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON u.id_rol = r.id_rol
            WHERE u.usuario = :usuario
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'usuario' => $usuario
        ]);

        $resultado = $consulta->fetch();

        return $resultado ?: null;
    }

   // Listamos los usuarios con búsqueda y filtros opcionales
public function listar(
    string $busqueda = '',
    ?int $idRol = null,
    string $estado = ''
): array {
    $sql = "
        SELECT
            u.id_usuario,
            u.nombre,
            u.apellido,
            u.correo,
            u.usuario,
            u.telefono,
            u.estado,
            u.fecha_registro,
            u.id_rol,
            r.nombre_rol
        FROM usuarios AS u
        INNER JOIN roles AS r
            ON u.id_rol = r.id_rol
        WHERE (
            u.nombre LIKE :buscar_nombre
            OR u.apellido LIKE :buscar_apellido
            OR u.correo LIKE :buscar_correo
            OR u.usuario LIKE :buscar_usuario
        )
    ";

    $patronBusqueda = '%' . $busqueda . '%';

    $parametros = [
        'buscar_nombre' => $patronBusqueda,
        'buscar_apellido' => $patronBusqueda,
        'buscar_correo' => $patronBusqueda,
        'buscar_usuario' => $patronBusqueda
    ];

    if ($idRol !== null) {
        $sql .= " AND u.id_rol = :id_rol";
        $parametros['id_rol'] = $idRol;
    }

    if ($estado !== '') {
        $sql .= " AND u.estado = :estado";
        $parametros['estado'] = $estado;
    }

    $sql .= " ORDER BY u.nombre ASC, u.apellido ASC";

    $consulta = $this->conexion->prepare($sql);
    $consulta->execute($parametros);

    return $consulta->fetchAll();
}

    // Obtenemos un usuario por su identificador
    public function buscarPorId(int $idUsuario): ?array
    {
        $sql = "
            SELECT
                id_usuario,
                id_rol,
                nombre,
                apellido,
                correo,
                usuario,
                telefono,
                estado
            FROM usuarios
            WHERE id_usuario = :id_usuario
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_usuario' => $idUsuario
        ]);

        $resultado = $consulta->fetch();

        return $resultado ?: null;
    }

    // Cargamos los roles disponibles para los formularios
    public function listarRoles(): array
    {
        $sql = "
            SELECT
                id_rol,
                nombre_rol
            FROM roles
            ORDER BY id_rol ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // Comprobamos que el nombre de usuario no esté repetido
    public function existeUsuario(
        string $usuario,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM usuarios
            WHERE usuario = :usuario
        ";

        $parametros = [
            'usuario' => $usuario
        ];

        if ($idExcluir !== null) {
            $sql .= " AND id_usuario != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Comprobamos que el correo electrónico no esté repetido
    public function existeCorreo(
        string $correo,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM usuarios
            WHERE correo = :correo
        ";

        $parametros = [
            'correo' => $correo
        ];

        if ($idExcluir !== null) {
            $sql .= " AND id_usuario != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Registramos los datos generales del usuario
    public function crear(
        int $idRol,
        string $nombre,
        string $apellido,
        string $correo,
        string $usuario,
        string $contrasenaHash,
        ?string $telefono
    ): bool {
        $sql = "
            INSERT INTO usuarios (
                id_rol,
                nombre,
                apellido,
                correo,
                usuario,
                contrasena_hash,
                telefono
            )
            VALUES (
                :id_rol,
                :nombre,
                :apellido,
                :correo,
                :usuario,
                :contrasena_hash,
                :telefono
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_rol' => $idRol,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'usuario' => $usuario,
            'contrasena_hash' => $contrasenaHash,
            'telefono' => $telefono
        ]);
    }

    // Actualizamos la información general sin cambiar la contraseña
    public function actualizar(
        int $idUsuario,
        int $idRol,
        string $nombre,
        string $apellido,
        string $correo,
        string $usuario,
        ?string $telefono,
        string $estado
    ): bool {
        $sql = "
            UPDATE usuarios
            SET
                id_rol = :id_rol,
                nombre = :nombre,
                apellido = :apellido,
                correo = :correo,
                usuario = :usuario,
                telefono = :telefono,
                estado = :estado
            WHERE id_usuario = :id_usuario
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_rol' => $idRol,
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $correo,
            'usuario' => $usuario,
            'telefono' => $telefono,
            'estado' => $estado,
            'id_usuario' => $idUsuario
        ]);
    }

    // Cambiamos la contraseña solamente cuando se solicita
    public function actualizarContrasena(
        int $idUsuario,
        string $contrasenaHash
    ): bool {
        $sql = "
            UPDATE usuarios
            SET contrasena_hash = :contrasena_hash
            WHERE id_usuario = :id_usuario
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'contrasena_hash' => $contrasenaHash,
            'id_usuario' => $idUsuario
        ]);
    }

    // Activamos o inactivamos usuarios sin borrar su historial
    public function cambiarEstado(
        int $idUsuario,
        string $estado
    ): bool {
        $sql = "
            UPDATE usuarios
            SET estado = :estado
            WHERE id_usuario = :id_usuario
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'estado' => $estado,
            'id_usuario' => $idUsuario
        ]);
    }
}