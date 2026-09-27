<?php
class ComprobanteModel
{
    private const ESTADOS = ['pendiente', 'aprobado', 'rechazado'];

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function registrar($id_estudiante, $monto, $fecha_pago, $ruta_archivo)
    {
        $monto = (float) $monto;
        if ($monto <= 0) {
            throw new InvalidArgumentException('El monto debe ser mayor a cero.');
        }
        if ($ruta_archivo === '') {
            throw new InvalidArgumentException('Debes adjuntar la imagen o PDF del comprobante.');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO comprobantes_pago_mg (id_estudiante, monto, fecha_pago, ruta_archivo, estado)
             VALUES (:id_estudiante, :monto, :fecha_pago, :ruta_archivo, 'pendiente')"
        );
        $stmt->execute([
            ':id_estudiante' => $id_estudiante,
            ':monto'         => $monto,
            ':fecha_pago'    => $fecha_pago,
            ':ruta_archivo'  => $ruta_archivo,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function obtenerPorEstudiante($id_estudiante)
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM comprobantes_pago_mg
             WHERE id_estudiante = :id_estudiante
             ORDER BY fecha_registro DESC"
        );
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return $stmt->fetchAll();
    }

    public function obtenerTodos()
    {
        $sql = "SELECT c.*, e.id_usuario, u.nombre, u.apellido, u.usuario, u.correo,
                       ue.nombre AS validador_nombre, ue.apellido AS validador_apellido
                FROM comprobantes_pago_mg c
                INNER JOIN estudiantes e ON c.id_estudiante = e.id_estudiante
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                LEFT JOIN usuarios ue ON c.validado_por = ue.id_usuario
                ORDER BY
                  CASE c.estado WHEN 'pendiente' THEN 0 ELSE 1 END,
                  c.fecha_registro DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtenerPorId($id_comprobante)
    {
        $sql = "SELECT c.*, e.id_usuario, u.nombre, u.apellido, u.usuario, u.correo,
                       ue.nombre AS validador_nombre, ue.apellido AS validador_apellido
                FROM comprobantes_pago_mg c
                INNER JOIN estudiantes e ON c.id_estudiante = e.id_estudiante
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                LEFT JOIN usuarios ue ON c.validado_por = ue.id_usuario
                WHERE c.id_comprobante = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_comprobante]);

        return $stmt->fetch();
    }

    public function validar($id_comprobante, $nuevo_estado, $id_operador, $motivo = null)
    {
        if (!in_array($nuevo_estado, self::ESTADOS, true)) {
            throw new InvalidArgumentException('El estado del comprobante no es válido.');
        }
        if ($nuevo_estado === 'rechazado' && trim((string) $motivo) === '') {
            throw new InvalidArgumentException('Debes indicar el motivo del rechazo.');
        }

        $stmt = $this->pdo->prepare(
            "UPDATE comprobantes_pago_mg
             SET estado = :estado, validado_por = :operador,
                 fecha_validacion = NOW(), motivo_rechazo = :motivo
             WHERE id_comprobante = :id"
        );
        return $stmt->execute([
            ':estado'   => $nuevo_estado,
            ':operador' => $id_operador,
            ':motivo'   => $nuevo_estado === 'aprobado' ? null : $motivo,
            ':id'       => $id_comprobante,
        ]);
    }

    public function tieneAprobado($id_estudiante)
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM comprobantes_pago_mg
             WHERE id_estudiante = :id_estudiante AND estado = 'aprobado'
             LIMIT 1"
        );
        $stmt->execute([':id_estudiante' => $id_estudiante]);

        return (bool) $stmt->fetchColumn();
    }

    public function contarPendientes()
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM comprobantes_pago_mg WHERE estado = 'pendiente'"
        );
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    /**
     * SPRINT 6: estados de comprobante disponibles (catálogo del dominio).
     */
    public static function estados(): array
    {
        return self::ESTADOS;
    }
}