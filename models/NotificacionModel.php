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
            ':mensaje'    => $this->limitarMensaje($mensaje),
            ':url'        => $url !== null ? trim((string) $url) : null,
        ]);
    }

    /** Recorta el texto al tamaño de la columna mensaje (VARCHAR 255). */
    private function limitarMensaje($mensaje)
    {
        $mensaje = trim((string) $mensaje);
        if (mb_strlen($mensaje) <= 255) {
            return $mensaje;
        }
        return mb_substr($mensaje, 0, 252) . '...';
    }

    private function obtenerNombreMateria($idMateria)
    {
        $stmt = $this->pdo->prepare('SELECT nombre_materia FROM materias WHERE id_materia = :id');
        $stmt->execute([':id' => (int) $idMateria]);
        $nombre = $stmt->fetchColumn();
        return $nombre ? (string) $nombre : 'Materia desconocida';
    }

    private function obtenerNombreEstudiante($idEstudiante)
    {
        $stmt = $this->pdo->prepare(
            'SELECT CONCAT(u.nombre, \' \', u.apellido) FROM estudiantes e
             INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
             WHERE e.id_estudiante = :id'
        );
        $stmt->execute([':id' => (int) $idEstudiante]);
        $nombre = $stmt->fetchColumn();
        return $nombre ? trim((string) $nombre) : 'Estudiante';
    }

    private function obtenerNombreTutor($idTutor)
    {
        $stmt = $this->pdo->prepare(
            'SELECT CONCAT(u.nombre, \' \', u.apellido) FROM tutores t
             INNER JOIN usuarios u ON t.id_usuario = u.id_usuario
             WHERE t.id_tutor = :id'
        );
        $stmt->execute([':id' => (int) $idTutor]);
        $nombre = $stmt->fetchColumn();
        return $nombre ? trim((string) $nombre) : 'Tutor';
    }

    private function obtenerIdsAdministradores()
    {
        $sql = "SELECT u.id_usuario
                FROM usuarios u
                INNER JOIN roles r ON u.id_rol = r.id_rol
                WHERE r.nombre_rol = 'administrador' AND u.estado = 'activo'";
        return array_map('intval', $this->pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN));
    }

    /**
     * Aviso al tutor cuando un estudiante solicita una tutoría.
     * Mensaje: "Nueva solicitud: Materia — Estudiante (Tutoría #id)"
     */
    public function generarNotificacionSolicitud($idUsuarioTutor, $idTutoria, $idMateria, $idEstudiante)
    {
        if (empty($idUsuarioTutor)) {
            return false;
        }
        $materia = $this->obtenerNombreMateria($idMateria);
        $estudiante = $this->obtenerNombreEstudiante($idEstudiante);
        $id = (int) $idTutoria;
        $mensaje = "Nueva solicitud: {$materia} — {$estudiante} (Tutoría #{$id})";
        return $this->crear($idUsuarioTutor, 'nueva_solicitud', $mensaje, '/views/tutor/panel.php');
    }

    /**
     * Aviso a todos los administradores activos sobre una solicitud nueva.
     * $excluirIdUsuario evita notificar al admin que acaba de registrar la tutoría.
     */
    public function generarNotificacionParaAdmin($idTutoria, $idMateria, $idEstudiante, $idTutor, $excluirIdUsuario = null)
    {
        $materia = $this->obtenerNombreMateria($idMateria);
        $estudiante = $this->obtenerNombreEstudiante($idEstudiante);
        $tutor = $this->obtenerNombreTutor($idTutor);
        $id = (int) $idTutoria;
        $mensaje = "Nueva solicitud: {$materia} — {$estudiante} con tutor {$tutor} (Tutoría #{$id})";
        $excluir = $excluirIdUsuario !== null ? (int) $excluirIdUsuario : 0;
        $ok = true;
        foreach ($this->obtenerIdsAdministradores() as $idAdmin) {
            if ($idAdmin === $excluir) {
                continue;
            }
            $ok = $this->crear($idAdmin, 'nueva_solicitud', $mensaje, '/controllers/tutorias_listar.php') && $ok;
        }
        return $ok;
    }

    /**
     * Notifica el cambio de estado a las partes que no ejecutaron la acción.
     * Flujo: pendiente → confirmada → en_proceso → realizada / detenido / cancelada.
     */
    public function notificarCambioEstado(array $tutoria, $nuevoEstado, $rolActor, $motivoCancelacion = '', $idUsuarioActor = 0)
    {
        $materia = $tutoria['nombre_materia'] ?? 'la tutoría';
        $id = (int) ($tutoria['id_tutoria'] ?? 0);
        $idEstudianteUsuario = (int) ($tutoria['estudiante_id_usuario'] ?? 0);
        $idTutorUsuario = (int) ($tutoria['tutor_id_usuario'] ?? 0);
        $nombreEst = trim(($tutoria['est_nombre'] ?? '') . ' ' . ($tutoria['est_apellido'] ?? ''));
        $nombreTut = trim(($tutoria['tut_nombre'] ?? '') . ' ' . ($tutoria['tut_apellido'] ?? ''));
        $actor = (int) $idUsuarioActor;
        $motivo = trim((string) $motivoCancelacion);

        $mensajesEstudiante = [
            'confirmada' => "Tu tutoría de {$materia} fue confirmada (Tutoría #{$id}).",
            'en_proceso' => "Tu tutoría de {$materia} está en proceso (Tutoría #{$id}).",
            'detenido'   => "Tu tutoría de {$materia} fue detenida (Tutoría #{$id}).",
            'realizada'  => "Tu tutoría de {$materia} se marcó como realizada. Ya puedes calificarla (Tutoría #{$id}).",
            'cancelada'  => "Una tutoría de {$materia} fue cancelada (Tutoría #{$id}). Motivo: {$motivo}",
        ];
        $mensajesTutor = [
            'confirmada' => "La tutoría de {$materia} con {$nombreEst} fue confirmada (Tutoría #{$id}).",
            'en_proceso' => "La tutoría de {$materia} con {$nombreEst} está en proceso (Tutoría #{$id}).",
            'detenido'   => "La tutoría de {$materia} con {$nombreEst} fue detenida (Tutoría #{$id}).",
            'realizada'  => "La tutoría de {$materia} con {$nombreEst} se marcó como realizada (Tutoría #{$id}).",
            'cancelada'  => "Una tutoría de {$materia} con {$nombreEst} fue cancelada (Tutoría #{$id}). Motivo: {$motivo}",
        ];
        $mensajeAdmin = "La tutoría #{$id} de {$materia} ({$nombreEst} / {$nombreTut}) pasó a {$nuevoEstado}.";
        if ($nuevoEstado === 'cancelada' && $motivo !== '') {
            $mensajeAdmin .= ' Motivo: ' . $motivo;
        }

        $urlEstudiante = '/views/estudiante/panel.php';
        $urlTutor = '/views/tutor/panel.php';
        $urlAdmin = '/controllers/tutorias_listar.php';

        if ($idEstudianteUsuario && $idEstudianteUsuario !== $actor && isset($mensajesEstudiante[$nuevoEstado])) {
            $this->crear($idEstudianteUsuario, $nuevoEstado, $mensajesEstudiante[$nuevoEstado], $urlEstudiante);
        }
        if ($idTutorUsuario && $idTutorUsuario !== $actor && isset($mensajesTutor[$nuevoEstado])) {
            $this->crear($idTutorUsuario, $nuevoEstado, $mensajesTutor[$nuevoEstado], $urlTutor);
        }
        foreach ($this->obtenerIdsAdministradores() as $idAdmin) {
            if ($idAdmin === $actor) {
                continue;
            }
            $this->crear($idAdmin, $nuevoEstado, $mensajeAdmin, $urlAdmin);
        }
        return true;
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
