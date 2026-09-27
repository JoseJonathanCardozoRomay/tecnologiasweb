<?php
class BitacoraModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_operador, $id_afectado, $accion, $detalles = null)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO bitacora_auditoria_usuarios (id_operador, id_afectado, accion, detalles, ip_origen)
                 VALUES (:id_operador, :id_afectado, :accion, :detalles, :ip_origen)"
            );
            return $stmt->execute([
                ':id_operador' => $id_operador,
                ':id_afectado' => $id_afectado,
                ':accion'      => $accion,
                ':detalles'    => $detalles !== null ? json_encode($detalles, JSON_UNESCAPED_UNICODE) : null,
                ':ip_origen'   => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerTodos()
    {
        $sql = "SELECT b.*,
                       op.nombre AS operador_nombre, op.apellido AS operador_apellido, op.usuario AS operador_usuario,
                       af.nombre AS afectado_nombre, af.apellido AS afectado_apellido, af.usuario AS afectado_usuario
                FROM bitacora_auditoria_usuarios b
                INNER JOIN usuarios op ON b.id_operador = op.id_usuario
                LEFT JOIN usuarios af ON b.id_afectado = af.id_usuario
                ORDER BY b.fecha_hora DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function ultimosRegistros(int $limite = 12)
    {
        $stmt = $this->pdo->prepare(
            "SELECT b.*,
                    op.nombre AS operador_nombre, op.apellido AS operador_apellido,
                    af.nombre AS afectado_nombre, af.apellido AS afectado_apellido
             FROM bitacora_auditoria_usuarios b
             INNER JOIN usuarios op ON b.id_operador = op.id_usuario
             LEFT JOIN usuarios af ON b.id_afectado = af.id_usuario
             ORDER BY b.fecha_hora DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * SPRINT 6: acciones presentes en la BD (dinámicas) para el filtro por
     * acción de la bitácora.
     */
    public function acciones()
    {
        $stmt = $this->pdo->prepare(
            "SELECT DISTINCT accion FROM bitacora_auditoria_usuarios WHERE accion <> '' ORDER BY accion"
        );
        $stmt->execute();

        return array_column($stmt->fetchAll(), 'accion');
    }
}