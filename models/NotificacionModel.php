<?php
declare(strict_types=1);

/** Acceso mínimo y seguro a las notificaciones internas del sistema. */
class NotificacionModel
{
    public function __construct(private PDO $pdo) {}

    public function contarNoLeidas(int $idUsuario): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM notificaciones WHERE id_usuario=:u AND leida=0');
        $stmt->execute([':u' => $idUsuario]);
        return (int)$stmt->fetchColumn();
    }

    public function obtenerPorUsuario(int $idUsuario): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM notificaciones WHERE id_usuario=:u ORDER BY fecha_creacion DESC LIMIT 50'
        );
        $stmt->execute([':u' => $idUsuario]);
        return $stmt->fetchAll();
    }

    public function marcarLeida(int $idNotificacion, int $idUsuario): bool
    {
        return $this->pdo->prepare(
            'UPDATE notificaciones SET leida=1 WHERE id_notificacion=:id AND id_usuario=:u'
        )->execute([':id'=>$idNotificacion, ':u'=>$idUsuario]);
    }

    /** Crea una notificación interna para un usuario. */
    public function crear(int $idUsuario, string $tipo, string $mensaje, ?string $url = null): bool
    {
        return $this->pdo->prepare(
            'INSERT INTO notificaciones(id_usuario,tipo,mensaje,url) VALUES(:u,:t,:m,:url)'
        )->execute([
            ':u'=>$idUsuario,
            ':t'=>$tipo,
            ':m'=>$mensaje,
            ':url'=>$url,
        ]);
    }

}
