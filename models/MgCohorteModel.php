<?php
class MgCohorteModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT c.id_cohorte_mg, c.nombre_periodo, c.fecha_inicio, c.fecha_fin,
                       c.estado, c.observaciones,
                       (SELECT COUNT(*) FROM expedientes_mg e
                        WHERE e.id_cohorte_mg = c.id_cohorte_mg) AS total_expedientes
                FROM cohortes_mg c
                ORDER BY c.fecha_inicio DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerTodasAbiertas()
    {
        $sql = "SELECT id_cohorte_mg, nombre_periodo, fecha_inicio, fecha_fin
                FROM cohortes_mg
                WHERE estado = 'abierta'
                ORDER BY fecha_inicio DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cohortes_mg WHERE id_cohorte_mg = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existePeriodo($nombrePeriodo, $excluirId = null)
    {
        $sql = "SELECT COUNT(*) FROM cohortes_mg WHERE nombre_periodo = :periodo";
        $params = [':periodo' => trim($nombrePeriodo)];
        if ($excluirId !== null) {
            $sql .= " AND id_cohorte_mg <> :id";
            $params[':id'] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function validarFechas($fechaInicio, $fechaFin)
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio)) {
            return false;
        }
        if ($fechaFin !== '' && $fechaFin !== null) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin)) {
                return false;
            }
            if ($fechaFin < $fechaInicio) {
                return false;
            }
        }
        return true;
    }

    public function crear($nombrePeriodo, $fechaInicio, $fechaFin, $estado, $observaciones)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO cohortes_mg (nombre_periodo, fecha_inicio, fecha_fin, estado, observaciones)
             VALUES (:periodo, :inicio, :fin, :estado, :observaciones)"
        );
        return $stmt->execute([
            ':periodo' => trim($nombrePeriodo),
            ':inicio' => $fechaInicio,
            ':fin' => $fechaFin !== '' ? $fechaFin : null,
            ':estado' => $estado,
            ':observaciones' => trim($observaciones),
        ]);
    }

    public function actualizar($id, $nombrePeriodo, $fechaInicio, $fechaFin, $estado, $observaciones)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE cohortes_mg
             SET nombre_periodo = :periodo, fecha_inicio = :inicio, fecha_fin = :fin,
                 estado = :estado, observaciones = :observaciones
             WHERE id_cohorte_mg = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':periodo' => trim($nombrePeriodo),
            ':inicio' => $fechaInicio,
            ':fin' => $fechaFin !== '' ? $fechaFin : null,
            ':estado' => $estado,
            ':observaciones' => trim($observaciones),
        ]);
    }

    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM cohortes_mg WHERE id_cohorte_mg = :id");
        return $stmt->execute([':id' => $id]);
    }
}