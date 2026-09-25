<?php

class TutorMateriaModel
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todas las materias junto con su carrera.
     */
    public function listarMaterias(): array
    {
        $sql = "
            SELECT
                m.id_materia,
                m.nombre_materia,
                m.id_carrera,
                COALESCE(
                    c.nombre_carrera,
                    'Sin carrera'
                ) AS nombre_carrera
            FROM materias AS m
            LEFT JOIN carreras AS c
                ON c.id_carrera = m.id_carrera
            ORDER BY
                c.nombre_carrera ASC,
                m.nombre_materia ASC
        ";

        $sentencia = $this->conexion->prepare($sql);
        $sentencia->execute();

        return $sentencia->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene los identificadores de las materias asignadas
     * actualmente a un tutor.
     */
    public function obtenerIdsAsignados(int $idTutor): array
    {
        $sql = "
            SELECT id_materia
            FROM tutor_materia
            WHERE id_tutor = :id_tutor
            ORDER BY id_materia ASC
        ";

        $sentencia = $this->conexion->prepare($sql);

        $sentencia->execute([
            'id_tutor' => $idTutor
        ]);

        $resultados = $sentencia->fetchAll(
            PDO::FETCH_COLUMN
        );

        return array_map(
            'intval',
            $resultados
        );
    }

    /**
     * Reemplaza las materias asignadas a un tutor.
     */
    public function actualizarAsignaciones(
        int $idTutor,
        array $idsMaterias
    ): bool {
        $idsMaterias = array_map(
            'intval',
            $idsMaterias
        );

        $idsMaterias = array_filter(
            $idsMaterias,
            fn(int $idMateria): bool => $idMateria > 0
        );

        $idsMaterias = array_values(
            array_unique($idsMaterias)
        );

        try {
            $this->conexion->beginTransaction();

            $eliminar = $this->conexion->prepare("
                DELETE FROM tutor_materia
                WHERE id_tutor = :id_tutor
            ");

            $eliminar->execute([
                'id_tutor' => $idTutor
            ]);

            if (!empty($idsMaterias)) {
                $insertar = $this->conexion->prepare("
                    INSERT INTO tutor_materia (
                        id_tutor,
                        id_materia
                    )
                    VALUES (
                        :id_tutor,
                        :id_materia
                    )
                ");

                foreach ($idsMaterias as $idMateria) {
                    $insertar->execute([
                        'id_tutor' => $idTutor,
                        'id_materia' => $idMateria
                    ]);
                }
            }

            $this->conexion->commit();

            return true;
        } catch (Throwable $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            throw $error;
        }
    }
}