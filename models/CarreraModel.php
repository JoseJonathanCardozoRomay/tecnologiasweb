<?php
declare(strict_types=1);

/**
 * Modelo de carreras académicas.
 *
 * Las carreras no se eliminan físicamente cuando ya forman parte del
 * historial académico: se marcan como inactivas.
 */
class CarreraModel
{
    public function __construct(private PDO $pdo) {}

    /**
     * Obtiene carreras filtradas opcionalmente por estado.
     */
    public function obtenerTodas(?string $estado = null): array
    {
        $sql = "SELECT c.id_carrera, c.nombre_carrera, c.estado,
                       (SELECT COUNT(*) FROM materias m WHERE m.id_carrera = c.id_carrera) AS total_materias,
                       (SELECT COUNT(*) FROM estudiantes e WHERE e.id_carrera = c.id_carrera) AS total_estudiantes
                FROM carreras c";
        $params = [];

        if ($estado !== null && in_array($estado, ['activo', 'inactivo'], true)) {
            $sql .= ' WHERE c.estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql .= ' ORDER BY c.id_carrera ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene carreras activas. Si se proporciona un ID actual, también
     * conserva esa carrera para que un registro antiguo pueda editarse.
     */
    public function obtenerActivas(?int $incluirId = null): array
    {
        $sql = 'SELECT id_carrera, nombre_carrera, estado FROM carreras WHERE estado = \'activo\'';
        $params = [];

        if ($incluirId !== null) {
            $sql .= ' OR id_carrera = :actual';
            $params[':actual'] = $incluirId;
        }

        $sql .= ' ORDER BY id_carrera ASC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM carreras WHERE id_carrera = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Comprueba si existe una carrera con el mismo nombre o con un nombre
     * suficientemente equivalente para evitar duplicados como:
     * "Ingeniería Comercial" y "Ing. Comercial".
     *
     * También detecta cuando un nombre contiene al otro después de
     * normalizar tildes, puntuación, espacios y abreviaturas habituales.
     */
    public function nombreSimilar(string $nombre, ?int $excepto = null): string|false
    {
        $sql = 'SELECT id_carrera, nombre_carrera FROM carreras';
        $params = [];
        if ($excepto !== null) {
            $sql .= ' WHERE id_carrera <> :id';
            $params[':id'] = $excepto;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $objetivo = $this->normalizarParaComparacion($nombre);

        foreach ($stmt->fetchAll() as $fila) {
            $existente = $this->normalizarParaComparacion((string)$fila['nombre_carrera']);
            if ($objetivo === '' || $existente === '') {
                continue;
            }

            if ($objetivo === $existente
                || str_contains($objetivo, $existente)
                || str_contains($existente, $objetivo)) {
                return (string)$fila['nombre_carrera'];
            }
        }

        return false;
    }

    public function existeNombre(string $nombre, ?int $excepto = null): bool
    {
        return $this->nombreSimilar($nombre, $excepto) !== false;
    }

    /**
     * Convierte nombres a una forma comparable sin modificar el valor
     * original almacenado en la base de datos.
     */
    private function normalizarParaComparacion(string $nombre): string
    {
        $nombre = trim($nombre);
        $nombre = strtr($nombre, [
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'Ü' => 'u', 'Ñ' => 'n',
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'ü' => 'u', 'ñ' => 'n',
        ]);
        $nombre = strtolower($nombre);
        $nombre = preg_replace('/[^a-z0-9]+/u', ' ', $nombre) ?? $nombre;
        $tokens = preg_split('/\s+/u', trim($nombre), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        // Abreviaturas académicas frecuentes.
        $abreviaturas = [
            'ing' => 'ingenieria',
        ];
        foreach ($tokens as &$token) {
            $token = $abreviaturas[$token] ?? $token;
        }
        unset($token);

        return implode('', $tokens);
    }

    public function crear(string $nombre): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO carreras (nombre_carrera, estado) VALUES (:nombre, 'activo')"
        );
        return $stmt->execute([':nombre' => $nombre]);
    }

    public function actualizar(int $id, string $nombre): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE carreras SET nombre_carrera = :nombre WHERE id_carrera = :id'
        );
        return $stmt->execute([':nombre' => $nombre, ':id' => $id]);
    }

    public function cambiarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            'UPDATE carreras SET estado = :estado WHERE id_carrera = :id'
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
}
