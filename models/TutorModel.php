<?php
class TutorModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        try {
            $sql = "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                           u.nombre, u.apellido, u.correo, u.estado
                    FROM tutores t
                    INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                    ORDER BY u.nombre ASC";

            $tutores = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tutores as &$tutor) {
                $tutor['materias'] = $this->materiasDelTutor($tutor['id_tutor']);
            }
            unset($tutor);

            return $tutores;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar los tutores.');
        }
    }

    public function obtenerPorId($id_tutor)
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT t.id_tutor, t.id_usuario, t.especialidad, t.biografia,
                        u.nombre, u.apellido, u.correo, u.telefono, u.estado
                 FROM tutores t
                 INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
                 WHERE t.id_tutor = :id_tutor"
            );
            $stmt->execute([':id_tutor' => $id_tutor]);
            $tutor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$tutor) {
                return null;
            }

            $tutor['materias'] = $this->materiasDelTutor($id_tutor);
            $tutor['disponibilidad'] = $this->disponibilidadDelTutor($id_tutor);

            return $tutor;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar el tutor.');
        }
    }

    public function crearPerfil($id_usuario, $especialidad, $biografia)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tutores (id_usuario, especialidad, biografia)
                 VALUES (:id_usuario, :especialidad, :biografia)"
            );
            $stmt->execute([
                ':id_usuario'   => $id_usuario,
                ':especialidad' => $especialidad,
                ':biografia'    => $biografia,
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Error al crear el perfil del tutor.');
        }
    }

    public function asignarMaterias($id_tutor, array $ids_materias)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("DELETE FROM tutor_materia WHERE id_tutor = :id_tutor");
            $stmt->execute([':id_tutor' => $id_tutor]);

            $insert = $this->pdo->prepare(
                "INSERT INTO tutor_materia (id_tutor, id_materia) VALUES (:id_tutor, :id_materia)"
            );

            foreach ($ids_materias as $id_materia) {
                $insert->execute([
                    ':id_tutor'   => $id_tutor,
                    ':id_materia' => $id_materia,
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al asignar las materias al tutor.');
        }
    }

    public function guardarDisponibilidad($id_tutor, array $bloques_horario)
    {
        try {
            $this->pdo->beginTransaction();

            $ids = array_values(array_filter(array_column($bloques_horario, 'id_disponibilidad')));

            $params = [$id_tutor];
            $sql = "DELETE FROM disponibilidad_tutor WHERE id_tutor = ?";

            if ($ids) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql .= " AND id_disponibilidad NOT IN ($placeholders)";
                $params = array_merge($params, $ids);
            }

            $this->pdo->prepare($sql)->execute($params);

            foreach ($bloques_horario as $bloque) {
                $dia   = $bloque['dia_semana'];
                $inicio = $bloque['hora_inicio'];
                $fin   = $bloque['hora_fin'];

                if (!empty($bloque['id_disponibilidad'])) {
                    $stmt = $this->pdo->prepare(
                        "UPDATE disponibilidad_tutor
                         SET dia_semana = :dia, hora_inicio = :inicio, hora_fin = :fin
                         WHERE id_disponibilidad = :id AND id_tutor = :id_tutor"
                    );
                    $stmt->execute([
                        ':dia'      => $dia,
                        ':inicio'   => $inicio,
                        ':fin'      => $fin,
                        ':id'       => $bloque['id_disponibilidad'],
                        ':id_tutor' => $id_tutor,
                    ]);
                } else {
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO disponibilidad_tutor (id_tutor, dia_semana, hora_inicio, hora_fin)
                         VALUES (:id_tutor, :dia, :inicio, :fin)"
                    );
                    $stmt->execute([
                        ':id_tutor' => $id_tutor,
                        ':dia'      => $dia,
                        ':inicio'   => $inicio,
                        ':fin'      => $fin,
                    ]);
                }
            }

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw new RuntimeException('Error al guardar la disponibilidad del tutor.');
        }
    }

    private function materiasDelTutor($id_tutor)
    {
        try {
            $sql = "SELECT m.id_materia, m.nombre_materia
                    FROM tutor_materia tm
                    INNER JOIN materias m ON tm.id_materia = m.id_materia
                    WHERE tm.id_tutor = :id_tutor
                    ORDER BY m.nombre_materia ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_tutor' => $id_tutor]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar las materias del tutor.');
        }
    }

    private function disponibilidadDelTutor($id_tutor)
    {
        try {
            $sql = "SELECT id_disponibilidad, dia_semana, hora_inicio, hora_fin
                    FROM disponibilidad_tutor
                    WHERE id_tutor = :id_tutor
                    ORDER BY FIELD(dia_semana, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'), hora_inicio ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_tutor' => $id_tutor]);
            $bloques = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($bloques as &$bloque) {
                $bloque['hora_inicio'] = $this->formatearHora($bloque['hora_inicio']);
                $bloque['hora_fin'] = $this->formatearHora($bloque['hora_fin']);
            }
            unset($bloque);

            return $bloques;
        } catch (PDOException $e) {
            throw new RuntimeException('Error al consultar la disponibilidad del tutor.');
        }
    }

    private function formatearHora($hora)
    {
        return $hora === null ? null : '1970-01-01 ' . substr($hora, 0, 8);
    }
}