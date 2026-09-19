<?php
class RolModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtenerTodos()
    {
        return $this->pdo->query("SELECT id_rol, nombre_rol FROM roles")->fetchAll();
    }

    public function existe($id)
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM roles WHERE id_rol = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return (bool) $stmt->fetchColumn();
    }
}