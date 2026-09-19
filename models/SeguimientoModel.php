<?php
class SeguimientoModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerPorTutoria($idTutoria)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM seguimiento_sesion WHERE id_tutoria = :id');
        $stmt->execute([':id' => (int) $idTutoria]);
        return $stmt->fetch();
    }

    public function registrar($idTutoria, $datos)
    {
        $sql = "INSERT INTO seguimiento_sesion (id_tutoria, asistio, temas_tratados, avance, recomendaciones)
                VALUES (:id_tutoria, :asistio, :temas_tratados, :avance, :recomendaciones)
                ON DUPLICATE KEY UPDATE
                    asistio = VALUES(asistio),
                    temas_tratados = VALUES(temas_tratados),
                    avance = VALUES(avance),
                    recomendaciones = VALUES(recomendaciones)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_tutoria'     => (int) $idTutoria,
            ':asistio'        => $datos['asistio'],
            ':temas_tratados' => $datos['temas_tratados'] ?? null,
            ':avance'         => $datos['avance'] ?? null,
            ':recomendaciones' => $datos['recomendaciones'] ?? null,
        ]);
    }
}
