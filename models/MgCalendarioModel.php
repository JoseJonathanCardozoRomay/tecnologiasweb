<?php
class MgCalendarioModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function listarPorCohorte($idCohorte)
    {
        $sql = "SELECT h.*, c.nombre_periodo
                FROM calendario_mg h
                JOIN cohortes_mg c ON c.id_cohorte_mg = h.id_cohorte_mg
                WHERE h.id_cohorte_mg = :cohorte
                ORDER BY h.fecha_hito ASC, h.orden ASC, h.id_hito ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cohorte' => $idCohorte]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM calendario_mg WHERE id_hito = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function contarPorCohorte($idCohorte)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM calendario_mg WHERE id_cohorte_mg = :cohorte");
        $stmt->execute([':cohorte' => $idCohorte]);
        return (int) $stmt->fetchColumn();
    }

    public function crear($idCohorte, $titulo, $descripcion, $tipo, $fecha, $orden)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO calendario_mg (id_cohorte_mg, titulo, descripcion, tipo_hito, fecha_hito, orden)
             VALUES (:cohorte, :titulo, :descripcion, :tipo, :fecha, :orden)"
        );
        return $stmt->execute([
            ':cohorte' => $idCohorte,
            ':titulo' => trim($titulo),
            ':descripcion' => trim($descripcion),
            ':tipo' => $tipo,
            ':fecha' => $fecha,
            ':orden' => $orden,
        ]);
    }

    public function actualizar($id, $titulo, $descripcion, $tipo, $fecha, $orden)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE calendario_mg
             SET titulo = :titulo, descripcion = :descripcion, tipo_hito = :tipo,
                 fecha_hito = :fecha, orden = :orden
             WHERE id_hito = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':titulo' => trim($titulo),
            ':descripcion' => trim($descripcion),
            ':tipo' => $tipo,
            ':fecha' => $fecha,
            ':orden' => $orden,
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM calendario_mg WHERE id_hito = :id");
        return $stmt->execute([':id' => $id]);
    }
}