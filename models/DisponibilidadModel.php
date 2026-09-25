<?php

class DisponibilidadModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los horarios registrados por un tutor.
     */
    public function listarPorTutor(int $idTutor): array
    {
        $sql = "
            SELECT
                id_disponibilidad,
                id_tutor,
                dia_semana,
                hora_inicio,
                hora_fin
            FROM disponibilidad_tutor
            WHERE id_tutor = :id_tutor
            ORDER BY
                FIELD(
                    dia_semana,
                    'Lunes',
                    'Martes',
                    'Miercoles',
                    'Jueves',
                    'Viernes',
                    'Sabado'
                ),
                hora_inicio ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_tutor' => $idTutor
        ]);

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un horario que pertenezca al tutor indicado.
     */
    public function buscarPorId(
        int $idDisponibilidad,
        int $idTutor
    ): ?array {
        $sql = "
            SELECT
                id_disponibilidad,
                id_tutor,
                dia_semana,
                hora_inicio,
                hora_fin
            FROM disponibilidad_tutor
            WHERE id_disponibilidad = :id_disponibilidad
                AND id_tutor = :id_tutor
            LIMIT 1
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_disponibilidad' => $idDisponibilidad,
            'id_tutor' => $idTutor
        ]);

        $disponibilidad = $sentencia->fetch(
            PDO::FETCH_ASSOC
        );

        return $disponibilidad ?: null;
    }

    /**
     * Comprueba si el horario se cruza con otro del mismo día.
     */
    public function existeCruce(
        int $idTutor,
        string $diaSemana,
        string $horaInicio,
        string $horaFin,
        ?int $idExcluir = null
    ): bool {
        $sql = "
            SELECT COUNT(*)
            FROM disponibilidad_tutor
            WHERE id_tutor = :id_tutor
                AND dia_semana = :dia_semana
                AND hora_inicio < :hora_fin
                AND hora_fin > :hora_inicio
        ";

        $parametros = [
            'id_tutor' => $idTutor,
            'dia_semana' => $diaSemana,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin
        ];

        if ($idExcluir !== null) {
            $sql .= "
                AND id_disponibilidad != :id_excluir
            ";

            $parametros['id_excluir'] = $idExcluir;
        }

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute($parametros);

        return (int) $sentencia->fetchColumn() > 0;
    }

    /**
     * Registra un nuevo horario para el tutor.
     */
    public function crear(
        int $idTutor,
        string $diaSemana,
        string $horaInicio,
        string $horaFin
    ): bool {
        $sql = "
            INSERT INTO disponibilidad_tutor (
                id_tutor,
                dia_semana,
                hora_inicio,
                hora_fin
            )
            VALUES (
                :id_tutor,
                :dia_semana,
                :hora_inicio,
                :hora_fin
            )
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_tutor' => $idTutor,
            'dia_semana' => $diaSemana,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin
        ]);
    }

    /**
     * Actualiza un horario que pertenece al tutor.
     */
    public function actualizar(
        int $idDisponibilidad,
        int $idTutor,
        string $diaSemana,
        string $horaInicio,
        string $horaFin
    ): bool {
        $sql = "
            UPDATE disponibilidad_tutor
            SET
                dia_semana = :dia_semana,
                hora_inicio = :hora_inicio,
                hora_fin = :hora_fin
            WHERE id_disponibilidad = :id_disponibilidad
                AND id_tutor = :id_tutor
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'dia_semana' => $diaSemana,
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
            'id_disponibilidad' => $idDisponibilidad,
            'id_tutor' => $idTutor
        ]);
    }

    /**
     * Elimina un horario que pertenece al tutor.
     */
    public function eliminar(
        int $idDisponibilidad,
        int $idTutor
    ): bool {
        $sql = "
            DELETE FROM disponibilidad_tutor
            WHERE id_disponibilidad = :id_disponibilidad
                AND id_tutor = :id_tutor
        ";

        $sentencia = $this->conexion->prepare($sql);

        return $sentencia->execute([
            'id_disponibilidad' => $idDisponibilidad,
            'id_tutor' => $idTutor
        ]);
    }
}