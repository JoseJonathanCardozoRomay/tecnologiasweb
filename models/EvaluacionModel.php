<?php
class EvaluacionModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerPorTutoria($id_tutoria)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM evaluaciones_tutoria WHERE id_tutoria = :id");
        $stmt->execute([':id' => $id_tutoria]);
        return $stmt->fetch();
    }

    public function registrar($id_tutoria, $calificacion, $comentario = '')
    {
        $sql = "INSERT INTO evaluaciones_tutoria (id_tutoria, calificacion, comentario)
                VALUES (:id_tutoria, :calif, :coment)
                ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), comentario = VALUES(comentario)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_tutoria' => $id_tutoria,
            ':calif'      => (int)$calificacion,
            ':coment'     => trim($comentario)
        ]);
    }

    public function obtenerPromedioPorTutor($id_tutor)
    {
        $sql = "SELECT AVG(ev.calificacion) AS promedio, COUNT(ev.id_evaluacion) AS total_evaluaciones
                FROM evaluaciones_tutoria ev
                INNER JOIN tutorias tu ON ev.id_tutoria = tu.id_tutoria
                WHERE tu.id_tutor = :id_tutor";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutor' => $id_tutor]);
        return $stmt->fetch();
    }
}
