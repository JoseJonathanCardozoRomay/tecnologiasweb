<?php
class NotificacionModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function crear($idUsuario, $tipo, $mensaje, $url = null)
    {
        $sql = "INSERT INTO notificaciones (id_usuario, tipo, mensaje, url)
                VALUES (:id_usuario, :tipo, :mensaje, :url)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => (int) $idUsuario,
            ':tipo'       => trim((string) $tipo),
            ':mensaje'    => trim((string) $mensaje),
            ':url'        => $url !== null ? trim((string) $url) : null,
        ]);
    }

    public function contarNoLeidas($idUsuario)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :id AND leida = 0');
        $stmt->execute([':id' => (int) $idUsuario]);
        return (int) $stmt->fetchColumn();
    }

    public function obtenerRecientes($idUsuario, $limite = 10)
    {
        $sql = "SELECT * FROM notificaciones
                WHERE id_usuario = :id
                ORDER BY fecha_creacion DESC, id_notificacion DESC
                LIMIT :limite";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obtenerPaginadas($idUsuario, $limite, $offset)
    {
        $sql = "SELECT * FROM notificaciones
                WHERE id_usuario = :id
                ORDER BY fecha_creacion DESC, id_notificacion DESC
                LIMIT :limite OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', (int) $idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contar($idUsuario)
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :id');
        $stmt->execute([':id' => (int) $idUsuario]);
        return (int) $stmt->fetchColumn();
    }

    public function marcarLeida($id, $idUsuario)
    {
        $stmt = $this->pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE id_notificacion = :id AND id_usuario = :id_usuario');
        return $stmt->execute([
            ':id'         => (int) $id,
            ':id_usuario' => (int) $idUsuario,
        ]);
    }

    public function marcarTodas($idUsuario)
    {
        $stmt = $this->pdo->prepare('UPDATE notificaciones SET leida = 1 WHERE id_usuario = :id AND leida = 0');
        return $stmt->execute([':id' => (int) $idUsuario]);
    }

    public function obtenerPorId($id, $idUsuario)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notificaciones WHERE id_notificacion = :id AND id_usuario = :id_usuario');
        $stmt->execute([
            ':id'         => (int) $id,
            ':id_usuario' => (int) $idUsuario,
        ]);
        return $stmt->fetch();
    }
}
