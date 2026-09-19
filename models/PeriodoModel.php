<?php
/**
 * PeriodoModel — Gestión de periodos académicos (rangos de fechas de tutoría).
 *
 * El coordinador/administrador define fecha_inicio y fecha_fin de cada periodo.
 * El estudiante SOLO puede elegir fechas dentro del rango del periodo activo.
 */
class PeriodoModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Devuelve los periodos activos ordenados por fecha de inicio. */
    public function obtenerActivos()
    {
        return $this->pdo->query("SELECT * FROM periodos_tutoria WHERE activo = 1 ORDER BY fecha_inicio ASC")->fetchAll();
    }

    /** Devuelve todos los periodos (activos e inactivos) para la gestión del admin. */
    public function obtenerTodos()
    {
        return $this->pdo->query("SELECT * FROM periodos_tutoria ORDER BY fecha_inicio DESC")->fetchAll();
    }

    /** Busca un periodo por su código (ej: 'I-2026'). */
    public function obtenerPorCodigo($codigo)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM periodos_tutoria WHERE codigo = :codigo");
        $stmt->execute([':codigo' => $codigo]);
        return $stmt->fetch();
    }

    /** Busca un periodo por su id. */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM periodos_tutoria WHERE id_periodo = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * Devuelve el periodo activo que CONTIENE la fecha dada.
     * Retorna false si ningún periodo activo cubre esa fecha.
     */
    public function obtenerActivoPorFecha($fecha)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM periodos_tutoria
             WHERE activo = 1 AND fecha_inicio <= :fecha_ini AND fecha_fin >= :fecha_fin
             ORDER BY fecha_inicio ASC LIMIT 1"
        );
        $stmt->execute([':fecha_ini' => $fecha, ':fecha_fin' => $fecha]);
        return $stmt->fetch();
    }

    public function existeCodigo($codigo, $excluirId = null)
    {
        $sql = 'SELECT 1 FROM periodos_tutoria WHERE codigo = :codigo';
        $params = [':codigo' => $codigo];
        if ($excluirId !== null) {
            $sql .= ' AND id_periodo <> :excluir';
            $params[':excluir'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    /** Crea un nuevo periodo. $datos: codigo, nombre, fecha_inicio, fecha_fin, activo, creado_por. */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO periodos_tutoria (codigo, nombre, fecha_inicio, fecha_fin, activo, creado_por)
             VALUES (:codigo, :nombre, :fecha_inicio, :fecha_fin, :activo, :creado_por)"
        );
        return $stmt->execute([
            ':codigo'       => trim($datos['codigo']),
            ':nombre'       => trim($datos['nombre']),
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin'    => $datos['fecha_fin'],
            ':activo'       => !empty($datos['activo']) ? 1 : 0,
            ':creado_por'   => $datos['creado_por'] ?? null,
        ]);
    }

    /** Actualiza un periodo existente. */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE periodos_tutoria
             SET codigo = :codigo, nombre = :nombre, fecha_inicio = :fecha_inicio,
                 fecha_fin = :fecha_fin, activo = :activo
             WHERE id_periodo = :id"
        );
        return $stmt->execute([
            ':codigo'       => trim($datos['codigo']),
            ':nombre'       => trim($datos['nombre']),
            ':fecha_inicio' => $datos['fecha_inicio'],
            ':fecha_fin'    => $datos['fecha_fin'],
            ':activo'       => !empty($datos['activo']) ? 1 : 0,
            ':id'           => $id,
        ]);
    }

    /** Activa o desactiva un periodo según el valor booleano recibido. */
    public function cambiarActivo($id, $activo)
    {
        $stmt = $this->pdo->prepare("UPDATE periodos_tutoria SET activo = :activo WHERE id_periodo = :id");
        return $stmt->execute([':activo' => $activo ? 1 : 0, ':id' => $id]);
    }

    /** Desactiva un periodo (no se elimina para conservar el histórico). */
    public function desactivar($id)
    {
        return $this->cambiarActivo($id, false);
    }
}
