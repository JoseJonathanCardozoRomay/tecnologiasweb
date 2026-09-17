<?php
class CarreraModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT c.id_carrera, c.nombre_carrera,
                       (SELECT COUNT(*) FROM materias m WHERE m.id_carrera = c.id_carrera) AS total_materias,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_carrera = c.id_carrera) AS total_estudiantes
                FROM carreras c
                ORDER BY c.nombre_carrera ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carreras WHERE id_carrera = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($nombre)
    {
        $stmt = $this->pdo->prepare("INSERT INTO carreras (nombre_carrera) VALUES (:nombre)");
        return $stmt->execute([':nombre' => trim($nombre)]);
    }

    public function actualizar($id, $nombre)
    {
        $stmt = $this->pdo->prepare("UPDATE carreras SET nombre_carrera = :nombre WHERE id_carrera = :id");
        return $stmt->execute([
            ':nombre' => trim($nombre),
            ':id'     => $id
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM carreras WHERE id_carrera = :id");
        return $stmt->execute([':id' => $id]);
    }
}
