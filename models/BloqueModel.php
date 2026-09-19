<?php
/**
 * BloqueModel — Gestión de bloques horarios (Morning / Noon / Afternoon / Night).
 *
 * Los bloques los define el administrador. El estudiante SOLO selecciona un bloque;
 * la hora_inicio/hora_fin de la tutoría se calculan automáticamente desde el bloque.
 */
class BloqueModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Devuelve todos los bloques ordenados por hora de inicio. */
    public function obtenerTodos()
    {
        return $this->pdo->query("SELECT * FROM bloques_horarios ORDER BY hora_inicio ASC")->fetchAll();
    }

    /** Busca un bloque por su id. Retorna false si no existe. */
    public function obtenerPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bloques_horarios WHERE id_bloque = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function existeNombre($nombre, $excluirId = null)
    {
        $sql = 'SELECT 1 FROM bloques_horarios WHERE nombre_bloque = :nombre';
        $params = [':nombre' => trim($nombre)];
        if ($excluirId !== null) {
            $sql .= ' AND id_bloque <> :excluir';
            $params[':excluir'] = $excluirId;
        }
        $sql .= ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    /** Crea un bloque. $datos: nombre_bloque, hora_inicio, hora_fin, descripcion. */
    public function crear($datos)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO bloques_horarios (nombre_bloque, hora_inicio, hora_fin, descripcion)
             VALUES (:nombre, :hora_inicio, :hora_fin, :descripcion)"
        );
        return $stmt->execute([
            ':nombre'      => trim($datos['nombre_bloque']),
            ':hora_inicio' => $datos['hora_inicio'],
            ':hora_fin'    => $datos['hora_fin'],
            ':descripcion' => trim($datos['descripcion'] ?? ''),
        ]);
    }

    /** Actualiza un bloque existente. */
    public function actualizar($id, $datos)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE bloques_horarios
             SET nombre_bloque = :nombre, hora_inicio = :hora_inicio,
                 hora_fin = :hora_fin, descripcion = :descripcion
             WHERE id_bloque = :id"
        );
        return $stmt->execute([
            ':nombre'      => trim($datos['nombre_bloque']),
            ':hora_inicio' => $datos['hora_inicio'],
            ':hora_fin'    => $datos['hora_fin'],
            ':descripcion' => trim($datos['descripcion'] ?? ''),
            ':id'          => $id,
        ]);
    }

    /** Cuenta las tutorías vinculadas a un bloque (para impedir borrar bloques en uso). */
    public function contarDependencias($id)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM tutorias WHERE id_bloque = :id');
        $stmt->execute([':id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    /** Elimina un bloque. Solo debe llamarse cuando no tiene tutorías asociadas. */
    public function eliminar($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM bloques_horarios WHERE id_bloque = :id");
        return $stmt->execute([':id' => $id]);
    }
}
