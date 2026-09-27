<?php
class AccesosModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        $sql = "SELECT ra.id_acceso, ra.id_usuario, ra.fecha_hora, ra.ip_origen, ra.resultado,
                       u.nombre, u.apellido, u.usuario
                FROM registro_accesos ra
                LEFT JOIN usuarios u ON ra.id_usuario = u.id_usuario
                WHERE ra.fecha_hora >= '2026-09-25 00:00:00'
                ORDER BY ra.fecha_hora DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * SPRINT 6: resultados de acceso presentes en la BD (dinámicos), para
     * construir las pestañas del filtro sin opciones hardcodeadas.
     */
    public function resultados()
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT resultado FROM registro_accesos WHERE resultado <> '' ORDER BY resultado"
        );
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'resultado');
    }
}