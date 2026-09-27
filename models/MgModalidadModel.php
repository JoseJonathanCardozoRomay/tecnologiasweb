<?php
class MgModalidadModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT m.id_modalidad_grado, m.nombre, m.requiere_tutor, m.flujo, m.activa,
                       (SELECT COUNT(*) FROM expedientes_mg e
                        WHERE e.id_modalidad_grado = m.id_modalidad_grado) AS total_expedientes
                FROM modalidades_grado m
                ORDER BY m.nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodasActivas()
    {
        $sql = "SELECT id_modalidad_grado, nombre, requiere_tutor, flujo
                FROM modalidades_grado
                WHERE activa = 1
                ORDER BY nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM modalidades_grado WHERE id_modalidad_grado = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeNombre($nombre, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM modalidades_grado WHERE nombre = :nombre";
        $params = [':nombre' => trim($nombre)];
        if ($excluirId !== null) {
            $sql .= " AND id_modalidad_grado <> :id";
            $params[':id'] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function crear($nombre, $requiereTutor, $flujo, $activa)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO modalidades_grado (nombre, requiere_tutor, flujo, activa)
             VALUES (:nombre, :requiere_tutor, :flujo, :activa)"
        );
        return $stmt->execute([
            ':nombre' => trim($nombre),
            ':requiere_tutor' => $requiereTutor ? 1 : 0,
            ':flujo' => $flujo,
            ':activa' => $activa ? 1 : 0,
        ]);
    }

    public function actualizar($id, $nombre, $requiereTutor, $flujo, $activa)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE modalidades_grado
             SET nombre = :nombre, requiere_tutor = :requiere_tutor, flujo = :flujo, activa = :activa
             WHERE id_modalidad_grado = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => trim($nombre),
            ':requiere_tutor' => $requiereTutor ? 1 : 0,
            ':flujo' => $flujo,
            ':activa' => $activa ? 1 : 0,
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM modalidades_grado WHERE id_modalidad_grado = :id");
        return $stmt->execute([':id' => $id]);
    }
}