<?php

class EstudianteModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // Listamos estudiantes con sus datos personales y académicos
    public function listar(
        string $busqueda = '',
        ?int $idCarrera = null
    ): array {
        $sql = "
            SELECT
                e.id_estudiante,
                e.id_usuario,
                e.id_carrera,
                e.semestre,
                e.registro_universitario,
                u.nombre,
                u.apellido,
                u.correo,
                u.usuario,
                u.estado,
                c.nombre_carrera
            FROM estudiantes AS e
            INNER JOIN usuarios AS u
                ON e.id_usuario = u.id_usuario
            INNER JOIN carreras AS c
                ON e.id_carrera = c.id_carrera
            WHERE (
                u.nombre LIKE :buscar_nombre
                OR u.apellido LIKE :buscar_apellido
                OR u.usuario LIKE :buscar_usuario
                OR e.registro_universitario LIKE :buscar_registro
            )
        ";

        $patronBusqueda = '%' . $busqueda . '%';

        $parametros = [
            'buscar_nombre' => $patronBusqueda,
            'buscar_apellido' => $patronBusqueda,
            'buscar_usuario' => $patronBusqueda,
            'buscar_registro' => $patronBusqueda
        ];

        if ($idCarrera !== null) {
            $sql .= " AND e.id_carrera = :id_carrera";
            $parametros['id_carrera'] = $idCarrera;
        }

        $sql .= " ORDER BY u.nombre ASC, u.apellido ASC";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    // Buscamos un perfil de estudiante mediante su identificador
    public function buscarPorId(int $idEstudiante): ?array
    {
        $sql = "
            SELECT
                e.id_estudiante,
                e.id_usuario,
                e.id_carrera,
                e.semestre,
                e.registro_universitario,
                u.nombre,
                u.apellido,
                u.usuario,
                u.estado,
                c.nombre_carrera
            FROM estudiantes AS e
            INNER JOIN usuarios AS u
                ON e.id_usuario = u.id_usuario
            INNER JOIN carreras AS c
                ON e.id_carrera = c.id_carrera
            WHERE e.id_estudiante = :id_estudiante
            LIMIT 1
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_estudiante' => $idEstudiante
        ]);

        $resultado = $consulta->fetch();

        return $resultado ?: null;
    }

    // Mostramos cuentas de estudiante que aún no tienen perfil
    public function listarUsuariosDisponibles(): array
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombre,
                u.apellido,
                u.usuario
            FROM usuarios AS u
            INNER JOIN roles AS r
                ON u.id_rol = r.id_rol
            WHERE r.nombre_rol = 'estudiante'
                AND u.estado = 'activo'
                AND NOT EXISTS (
                    SELECT 1
                    FROM estudiantes AS e
                    WHERE e.id_usuario = u.id_usuario
                )
            ORDER BY u.nombre ASC, u.apellido ASC
        ";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // Comprobamos que el registro universitario no esté repetido
    public function existeRegistro(
        string $registroUniversitario,
        ?int $idExcluir = null
    ): bool {
        if ($registroUniversitario === '') {
            return false;
        }

        $sql = "
            SELECT COUNT(*)
            FROM estudiantes
            WHERE registro_universitario = :registro
        ";

        $parametros = [
            'registro' => $registroUniversitario
        ];

        if ($idExcluir !== null) {
            $sql .= " AND id_estudiante != :id_excluir";
            $parametros['id_excluir'] = $idExcluir;
        }

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($parametros);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Creamos el perfil académico del estudiante
    public function crear(
        int $idUsuario,
        int $idCarrera,
        int $semestre,
        ?string $registroUniversitario
    ): bool {
        $sql = "
            INSERT INTO estudiantes (
                id_usuario,
                id_carrera,
                semestre,
                registro_universitario
            )
            VALUES (
                :id_usuario,
                :id_carrera,
                :semestre,
                :registro_universitario
            )
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_usuario' => $idUsuario,
            'id_carrera' => $idCarrera,
            'semestre' => $semestre,
            'registro_universitario' => $registroUniversitario
        ]);
    }

    // Actualizamos los datos académicos del estudiante
    public function actualizar(
        int $idEstudiante,
        int $idCarrera,
        int $semestre,
        ?string $registroUniversitario
    ): bool {
        $sql = "
            UPDATE estudiantes
            SET
                id_carrera = :id_carrera,
                semestre = :semestre,
                registro_universitario = :registro_universitario
            WHERE id_estudiante = :id_estudiante
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_carrera' => $idCarrera,
            'semestre' => $semestre,
            'registro_universitario' => $registroUniversitario,
            'id_estudiante' => $idEstudiante
        ]);
    }

    // Revisamos si el estudiante tiene tutorías registradas
    public function tieneTutorias(int $idEstudiante): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM tutorias
            WHERE id_estudiante = :id_estudiante
        ";

        $consulta = $this->conexion->prepare($sql);

        $consulta->execute([
            'id_estudiante' => $idEstudiante
        ]);

        return (int) $consulta->fetchColumn() > 0;
    }

    // Eliminamos únicamente el perfil académico
    public function eliminar(int $idEstudiante): bool
    {
        $sql = "
            DELETE FROM estudiantes
            WHERE id_estudiante = :id_estudiante
        ";

        $consulta = $this->conexion->prepare($sql);

        return $consulta->execute([
            'id_estudiante' => $idEstudiante
        ]);
    }
}