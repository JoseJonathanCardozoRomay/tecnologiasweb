<?php
declare(strict_types=1);

/**
 * Acceso a los roles disponibles en el sistema.
 */
class RolModel
{
    public function __construct(private PDO $pdo) {}

    /**
     * Obtiene todos los roles para formularios administrativos.
     */
    public function obtenerTodos(): array
    {
        $stmt = $this->pdo->query(
            'SELECT id_rol, nombre_rol FROM roles ORDER BY id_rol ASC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Busca un rol por su identificador.
     *
     * @return array|false
     */
    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_rol, nombre_rol FROM roles WHERE id_rol = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
