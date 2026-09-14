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
}