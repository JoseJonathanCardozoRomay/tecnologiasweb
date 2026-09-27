<?php
class NotificationModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function crear(int $id_destinatario, string $tipo, string $mensaje, ?string $enlace = null, ?int $id_origen = null): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO notificaciones (id_destinatario, id_origen, tipo, mensaje, enlace)
                 VALUES (:id_destinatario, :id_origen, :tipo, :mensaje, :enlace)"
            );
            return $stmt->execute([
                ':id_destinatario' => $id_destinatario,
                ':id_origen'       => $id_origen,
                ':tipo'            => $tipo,
                ':mensaje'         => mb_substr($mensaje, 0, 500),
                ':enlace'          => $enlace,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function noLeidas(int $id_usuario): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE id_destinatario = :id_user AND leida = 0"
        );
        $stmt->execute([':id_user' => $id_usuario]);

        return (int) $stmt->fetchColumn();
    }

    public function recientes(int $id_usuario, int $limite = 8): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT n.*, u.nombre AS origen_nombre, u.apellido AS origen_apellido
             FROM notificaciones n
             LEFT JOIN usuarios u ON n.id_origen = u.id_usuario
             WHERE n.id_destinatario = :id_user
             ORDER BY n.fecha DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':id_user', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function marcarLeida(int $id_notificacion, int $id_usuario): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE notificaciones SET leida = 1
             WHERE id_notificacion = :id_notificacion AND id_destinatario = :id_user"
        );

        return $stmt->execute([':id_notificacion' => $id_notificacion, ':id_user' => $id_usuario]);
    }

    public function marcarTodasLeidas(int $id_usuario): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE id_destinatario = :id_user"
        );

        return $stmt->execute([':id_user' => $id_usuario]);
    }

    public function obtenerTodas(int $id_usuario): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT n.*, u.nombre AS origen_nombre, u.apellido AS origen_apellido
             FROM notificaciones n
             LEFT JOIN usuarios u ON n.id_origen = u.id_usuario
             WHERE n.id_destinatario = :id_user
             ORDER BY n.fecha DESC, n.id_notificacion DESC"
        );
        $stmt->execute([':id_user' => $id_usuario]);

        return $stmt->fetchAll();
    }

    public function idUsuarioDeEstudiante(int $id_estudiante): int
    {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM estudiantes WHERE id_estudiante = :id_estudiante LIMIT 1");
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function usuariosDeTutoria(int $id_tutoria): array
    {
        $sql = "SELECT t.id_tutoria,
                       e.id_usuario AS id_usuario_estudiante,
                       tut.id_usuario AS id_usuario_tutor
                FROM tutorias t
                INNER JOIN estudiantes e ON t.id_estudiante = e.id_estudiante
                INNER JOIN tutores tt ON t.id_tutor = tt.id_tutor
                INNER JOIN usuarios tut ON tt.id_usuario = tut.id_usuario
                WHERE t.id_tutoria = :id_tutoria";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_tutoria' => (int) $id_tutoria]);

        return $stmt->fetch() ?: [];
    }

    public function idsUsuariosAdministracion(): array
    {
        $stmt = $this->pdo->prepare("SELECT id_usuario FROM usuarios WHERE id_rol IN (1, 4)");
        $stmt->execute();

        return array_map('intval', array_column($stmt->fetchAll(), 'id_usuario'));
    }
}