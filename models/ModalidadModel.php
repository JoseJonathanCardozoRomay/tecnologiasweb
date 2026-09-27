<?php
class ModalidadModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodas()
    {
        $sql = "SELECT id_modalidad, nombre, descripcion, minimo_reuniones_semana,
                       cantidad_informes, activa
                FROM modalidades_graduacion
                WHERE activa = 1
                ORDER BY nombre ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id_modalidad)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM modalidades_graduacion WHERE id_modalidad = :id");
        $stmt->execute([':id' => $id_modalidad]);
        return $stmt->fetch();
    }
}