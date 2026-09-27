<?php
class HistorialModel
{
    private const CATEGORIAS = [
        'Sesiones y Acceso' => ['LOGIN', 'LOGOUT'],
        'Gestión de Cartas y Pagos' => [
            'CARTA_ACEPTADA', 'CARTA_RECHAZADA', 'COMPROBANTE_MG', 'COMPROBANTE_MG_APROBADO',
        ],
        'Seguimiento Académico' => ['REUNION_REGISTRADA', 'INFORME_REGISTRADO'],
        'Tribunales y Tutores' => [
            'TRIBUNAL_ASIGNADO', 'TRIBUNAL_MODIFICADO', 'TRIBUNAL_ELIMINADO',
            'TUTORIA_ASIGNADA', 'TUTORIA_UNIDO', 'TUTORIA_CANCELADA', 'TUTORIA_FINALIZADA',
        ],
    ];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_usuario, string $tipo_evento, string $descripcion, string $ip_origen = null)
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO historial_auditoria (id_usuario, tipo_evento, descripcion, ip_origen)
                 VALUES (:id_usuario, :tipo_evento, :descripcion, :ip_origen)"
            );

            return $stmt->execute([
                ':id_usuario'  => $id_usuario,
                ':tipo_evento' => $tipo_evento,
                ':descripcion' => $descripcion,
                ':ip_origen'   => $ip_origen ?? ($_SERVER['REMOTE_ADDR'] ?? null),
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function obtenerTodos()
    {
        $sql = "SELECT h.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, u.usuario
                FROM historial_auditoria h
                LEFT JOIN usuarios u ON h.id_usuario = u.id_usuario
                ORDER BY h.fecha_hora DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function tiposEvento(): array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT tipo_evento FROM historial_auditoria ORDER BY tipo_evento");
        return array_column($stmt->execute() ? $stmt->fetchAll() : [], 'tipo_evento');
    }

    public function categorias(): array
    {
        $conocidos = array_merge(...array_values(self::CATEGORIAS));
        $existentes = $this->tiposEvento();
        $noClasificados = array_values(array_filter(
            array_diff($existentes, $conocidos),
            fn($t) => $t !== ''
        ));

        $resultado = self::CATEGORIAS;
        if (!empty($noClasificados)) {
            $resultado['Otros eventos'] = $noClasificados;
        }

        return $resultado;
    }

    /**
     * Consulta con filtros combinados. Todo en prepared statements (params
     * enlazados); $busqueda_usuario y $id_rol (SPRINT 6) se filtran sobre la
     * tabla usuarios mediante LIKE enlazado para evitar SQL Injection.
     */
    public function consultar($tipo_evento = null, $id_usuario = null, $fecha_desde = null, $fecha_hasta = null, $busqueda_usuario = null, $id_rol = null)
    {
        $sql = "SELECT h.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido, u.usuario, u.id_rol
                FROM historial_auditoria h
                LEFT JOIN usuarios u ON h.id_usuario = u.id_usuario
                WHERE 1 = 1";
        $params = [];

        if ($tipo_evento !== null && $tipo_evento !== '') {
            if (is_array($tipo_evento)) {
                $tipos = array_values(array_filter(array_map('trim', $tipo_evento), fn($t) => $t !== ''));
                if (!empty($tipos)) {
                    $marcadores = [];
                    foreach ($tipos as $i => $tipo) {
                        $clave = ':tipo_evento_' . $i;
                        $marcadores[] = $clave;
                        $params[$clave] = $tipo;
                    }
                    $sql .= " AND h.tipo_evento IN (" . implode(', ', $marcadores) . ")";
                }
            } else {
                $sql .= " AND h.tipo_evento = :tipo_evento";
                $params[':tipo_evento'] = $tipo_evento;
            }
        }

        if ($id_usuario !== null && $id_usuario !== '') {
            $sql .= " AND h.id_usuario = :id_usuario";
            $params[':id_usuario'] = $id_usuario;
        }

        if ($id_rol !== null && $id_rol !== '' && (int) $id_rol > 0) {
            $sql .= " AND u.id_rol = :id_rol";
            $params[':id_rol'] = (int) $id_rol;
        }

        if ($busqueda_usuario !== null && $busqueda_usuario !== '') {
            $busqueda = '%' . escapaLike(trim($busqueda_usuario)) . '%';
            $sql .= " AND (u.nombre LIKE :busq_nombre OR u.apellido LIKE :busq_apellido
                           OR u.usuario LIKE :busq_usuario)";
            $params[':busq_nombre'] = $busqueda;
            $params[':busq_apellido'] = $busqueda;
            $params[':busq_usuario'] = $busqueda;
        }

        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(h.fecha_hora) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }

        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(h.fecha_hora) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }

        $sql .= " ORDER BY h.fecha_hora DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function ultimosEventos(int $limite = 8)
    {
        $stmt = $this->pdo->prepare(
            "SELECT h.*, u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
             FROM historial_auditoria h
             LEFT JOIN usuarios u ON h.id_usuario = u.id_usuario
             ORDER BY h.fecha_hora DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}