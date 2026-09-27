<?php
class ConfiguracionModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtener($clave)
    {
        $stmt = $this->pdo->prepare("SELECT valor FROM configuracion_sistema WHERE clave = :clave LIMIT 1");
        $stmt->execute([':clave' => $clave]);

        return $stmt->fetchColumn();
    }

    public function obtenerTodas()
    {
        return $this->pdo->query("SELECT clave, valor, descripcion FROM configuracion_sistema ORDER BY clave ASC")->fetchAll();
    }

    public function int($clave, $defecto = 0)
    {
        $valor = $this->obtener($clave);

        return $valor === false ? (int) $defecto : (int) $valor;
    }
}