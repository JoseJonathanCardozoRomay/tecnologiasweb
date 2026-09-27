<?php
class MgParametroModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function obtener($clave)
    {
        $stmt = $this->pdo->prepare("SELECT valor FROM parametros_mg WHERE clave = :clave LIMIT 1");
        $stmt->execute([':clave' => $clave]);

        return $stmt->fetchColumn();
    }

    public function obtenerTodas()
    {
        $sql = "SELECT clave, valor, descripcion, tipo
                FROM parametros_mg
                ORDER BY CASE tipo
                            WHEN 'entero' THEN 1
                            WHEN 'decimal' THEN 2
                            WHEN 'booleano' THEN 3
                            ELSE 4
                          END, clave ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function actualizar($clave, $valor)
    {
        $stmt = $this->pdo->prepare("UPDATE parametros_mg SET valor = :valor WHERE clave = :clave");
        return $stmt->execute([
            ':clave' => $clave,
            ':valor' => trim((string) $valor),
        ]);
    }

    public function int($clave, $defecto = 0)
    {
        $valor = $this->obtener($clave);

        return $valor === false ? (int) $defecto : (int) $valor;
    }

    public function booleano($clave, $defecto = false)
    {
        $valor = $this->obtener($clave);

        return $valor === false ? (bool) $defecto : (bool) (int) $valor;
    }
}