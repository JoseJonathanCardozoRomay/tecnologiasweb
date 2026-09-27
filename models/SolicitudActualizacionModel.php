<?php
class SolicitudActualizacionModel
{
    private const TIPOS = ['materias', 'horarios', 'carreras'];
    private const ESTADOS = ['pendiente', 'aprobada', 'rechazada'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_tutor, $id_usuario, $tipo, $detalle)
    {
        if (!in_array($tipo, self::TIPOS, true)) {
            throw new InvalidArgumentException('El tipo de solicitud no es válido.');
        }

        $detalle = trim((string) $detalle);
        if ($detalle === '') {
            throw new InvalidArgumentException('Describe el cambio que necesitas para que el administrador pueda atenderlo.');
        }

        $pendiente = $this->pdo->prepare(
            "SELECT id_solicitud FROM solicitudes_actualizacion_tutor
             WHERE id_tutor = :id_tutor AND estado = 'pendiente'
             LIMIT 1"
        );
        $pendiente->execute([':id_tutor' => (int) $id_tutor]);
        if ($pendiente->fetch()) {
            throw new RuntimeException('Ya tienes una solicitud pendiente. Espera la respuesta del administrador.');
        }

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO solicitudes_actualizacion_tutor
                    (id_tutor, id_usuario_solicitante, tipo, detalle)
                 VALUES
                    (:id_tutor, :id_usuario, :tipo, :detalle)"
            );
            $stmt->execute([
                ':id_tutor'   => (int) $id_tutor,
                ':id_usuario' => (int) $id_usuario,
                ':tipo'       => $tipo,
                ':detalle'    => $detalle,
            ]);

            return (int) $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo registrar la solicitud.');
        }
    }

    public function responder($id_solicitud, $nuevo_estado, $id_usuario, $respuesta = null)
    {
        if (!in_array($nuevo_estado, ['aprobada', 'rechazada'], true)) {
            throw new InvalidArgumentException('El estado de la solicitud no es válido.');
        }
        if ($nuevo_estado === 'rechazada' && trim((string) $respuesta) === '') {
            throw new InvalidArgumentException('Debes indicar el motivo del rechazo.');
        }

        try {
            $stmt = $this->pdo->prepare(
                "UPDATE solicitudes_actualizacion_tutor
                    SET estado = :estado,
                        respuesta = :respuesta,
                        id_usuario_responde = :id_usuario,
                        fecha_respuesta = CURRENT_TIMESTAMP
                  WHERE id_solicitud = :id_solicitud
                    AND estado = 'pendiente'"
            );
            $stmt->execute([
                ':estado'        => $nuevo_estado,
                ':respuesta'     => trim((string) $respuesta) !== '' ? trim((string) $respuesta) : null,
                ':id_usuario'    => (int) $id_usuario,
                ':id_solicitud'  => (int) $id_solicitud,
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo actualizar la solicitud.');
        }
    }

    public function obtenerPorId($id_solicitud)
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.*,
                    tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                    us.nombre AS solicitante_nombre, us.apellido AS solicitante_apellido,
                    ur.nombre AS responde_nombre, ur.apellido AS responde_apellido,
                    (SELECT c.nombre_carrera
                       FROM docente_carreras dc
                       INNER JOIN carreras c ON c.id_carrera = dc.id_carrera
                      WHERE dc.id_tutor = tu.id_tutor
                      ORDER BY c.nombre_carrera
                      LIMIT 1) AS nombre_carrera
             FROM solicitudes_actualizacion_tutor s
             INNER JOIN tutores tu ON tu.id_tutor = s.id_tutor
             INNER JOIN usuarios tut ON tut.id_usuario = tu.id_usuario
             LEFT JOIN usuarios us ON us.id_usuario = s.id_usuario_solicitante
             LEFT JOIN usuarios ur ON ur.id_usuario = s.id_usuario_responde
             WHERE s.id_solicitud = :id_solicitud
             LIMIT 1"
        );
        $stmt->execute([':id_solicitud' => (int) $id_solicitud]);

        return $stmt->fetch();
    }

    public function listar($id_tutor = null, $estado = null)
    {
        $sql = "SELECT s.*,
                       tut.nombre AS tutor_nombre, tut.apellido AS tutor_apellido,
                       us.nombre AS solicitante_nombre, us.apellido AS solicitante_apellido,
                       ur.nombre AS responde_nombre, ur.apellido AS responde_apellido,
                       (SELECT c.nombre_carrera
                          FROM docente_carreras dc
                          INNER JOIN carreras c ON c.id_carrera = dc.id_carrera
                         WHERE dc.id_tutor = tu.id_tutor
                         ORDER BY c.nombre_carrera
                         LIMIT 1) AS nombre_carrera
                FROM solicitudes_actualizacion_tutor s
                INNER JOIN tutores tu ON tu.id_tutor = s.id_tutor
                INNER JOIN usuarios tut ON tut.id_usuario = tu.id_usuario
                LEFT JOIN usuarios us ON us.id_usuario = s.id_usuario_solicitante
                LEFT JOIN usuarios ur ON ur.id_usuario = s.id_usuario_responde";
        $params = [];

        if ($id_tutor !== null) {
            $sql .= ' WHERE s.id_tutor = :id_tutor';
            $params[':id_tutor'] = (int) $id_tutor;
        }
        if ($estado !== null && in_array($estado, self::ESTADOS, true)) {
            $sql .= ($id_tutor !== null ? ' AND' : ' WHERE') . ' s.estado = :estado';
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY FIELD(s.estado, 'pendiente', 'aprobada', 'rechazada'), s.fecha_solicitud DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function contarPendientes()
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM solicitudes_actualizacion_tutor WHERE estado = 'pendiente'"
        );

        return (int) $stmt->fetchColumn();
    }

    public static function etiquetaTipo($tipo)
    {
        $etiquetas = [
            'materias'  => 'Materias',
            'horarios'  => 'Horarios',
            'carreras'  => 'Carreras',
        ];

        return $etiquetas[(string) $tipo] ?? ucfirst((string) $tipo);
    }

    public static function claseEstado($estado)
    {
        $clases = [
            'pendiente' => 'status-badge status-pending',
            'aprobada'  => 'status-badge status-completed',
            'rechazada' => 'status-badge status-cancelled',
        ];

        return $clases[(string) $estado] ?? 'status-badge status-neutral';
    }
}
