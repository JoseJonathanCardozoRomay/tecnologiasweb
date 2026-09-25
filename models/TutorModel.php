<?php

class TutorModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los tutores registrados junto con sus datos de usuario.
     */
    public function listar(string $busqueda = ''): array
    {
        $sql = '
            SELECT
                t.id_tutor,
                t.id_usuario,
                t.especialidad,
                t.biografia,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,
                u.telefono,
                u.estado
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            WHERE
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR u.usuario LIKE :buscar_usuario
                OR t.especialidad LIKE :buscar_especialidad
            ORDER BY
                u.nombre ASC,
                u.apellido ASC
        ';

        $sentencia = $this->conexion->prepare($sql);
        $patronBusqueda = '%' . trim($busqueda) . '%';

        $sentencia->execute([
            'buscar_nombre' => $patronBusqueda,
            'buscar_apellido' => $patronBusqueda,
            'buscar_usuario' => $patronBusqueda,
            'buscar_especialidad' => $patronBusqueda
        ]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un perfil de tutor por su identificador.
     */
    public function buscarPorId(int $idTutor): ?array
    {
        $sql = '
            SELECT
                t.id_tutor,
                t.id_usuario,
                t.especialidad,
                t.biografia,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,
                u.telefono,
                u.estado
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            WHERE t.id_tutor = :id_tutor
            LIMIT 1
        ';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_tutor' => $idTutor
        ]);

        $tutor = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $tutor ?: null;
    }

    /**
     * Busca el perfil de tutor asociado a una cuenta de usuario.
     */
    public function buscarPorUsuario(int $idUsuario): ?array
    {
        $sql = '
            SELECT
                t.id_tutor,
                t.id_usuario,
                t.especialidad,
                t.biografia,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,
                u.telefono,
                u.estado
            FROM tutores AS t
            INNER JOIN usuarios AS u
                ON u.id_usuario = t.id_usuario
            WHERE t.id_usuario = :id_usuario
            LIMIT 1
        ';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_usuario' => $idUsuario
        ]);

        $tutor = $sentencia->fetch(PDO::FETCH_ASSOC);

        return $tutor ?: null;
    }

    /**
     * Lista cuentas activas con rol tutor que todavía no tienen perfil.
     */
    public function listarUsuariosDisponibles(): array
    {
        $sql = '
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON u.id_rol = r.id_rol
            WHERE
                r.nombre_rol = :rol
                AND u.estado = :estado
                AND NOT EXISTS (
                    SELECT 1
                    FROM tutores AS t
                    WHERE t.id_usuario = u.id_usuario
                )
            ORDER BY
                u.nombre ASC,
                u.apellido ASC
        ';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'rol' => 'tutor',
            'estado' => 'activo'
        ]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea el perfil académico de un tutor.
     */
    public function crear(
        int $idUsuario,
        ?string $especialidad,
        ?string $biografia
    ): bool {
        $sql = '
            INSERT INTO tutores (
                id_usuario,
                especialidad,
                biografia
            )
            VALUES (
                :id_usuario,
                :especialidad,
                :biografia
            )
        ';

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_usuario' => $idUsuario,
            'especialidad' => $especialidad,
            'biografia' => $biografia
        ]);
    }

    /**
     * Actualiza la información académica del tutor.
     */
    public function actualizar(
        int $idTutor,
        ?string $especialidad,
        ?string $biografia
    ): bool {
        $sql = '
            UPDATE tutores
            SET
                especialidad = :especialidad,
                biografia = :biografia
            WHERE id_tutor = :id_tutor
        ';

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_tutor' => $idTutor,
            'especialidad' => $especialidad,
            'biografia' => $biografia
        ]);
    }

    /**
     * Comprueba si el tutor tiene tutorías registradas.
     */
    public function tieneTutorias(int $idTutor): bool
    {
        $sql = '
            SELECT COUNT(*)
            FROM tutorias
            WHERE id_tutor = :id_tutor
        ';

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_tutor' => $idTutor
        ]);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Elimina únicamente el perfil de tutor.
     * La cuenta de usuario se conserva.
     */
    public function eliminar(int $idTutor): bool
    {
        $sql = '
            DELETE FROM tutores
            WHERE id_tutor = :id_tutor
        ';

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_tutor' => $idTutor
        ]);
    }
}