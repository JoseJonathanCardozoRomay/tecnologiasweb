<?php
class DocumentoExpedienteModel
{
    private const EXTENSIONES_PERMITIDAS = ['doc', 'docx', 'pdf'];
    private const TAMANIO_MAXIMO = 5242880; // 5 MB

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function extensionesPermitidas(): array
    {
        return self::EXTENSIONES_PERMITIDAS;
    }

    public function tamanioMaximo(): int
    {
        return self::TAMANIO_MAXIMO;
    }

    public function registrar(int $id_tutoria, int $id_origen, int $id_destinatario, string $nombre_original, string $ruta_archivo, ?string $descripcion): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO documentos_expediente
                (id_tutoria, id_origen, id_destinatario, nombre_original, ruta_archivo, descripcion)
             VALUES (:id_tutoria, :id_origen, :id_destinatario, :nombre_original, :ruta_archivo, :descripcion)"
        );
        $stmt->execute([
            ':id_tutoria'      => $id_tutoria,
            ':id_origen'       => $id_origen,
            ':id_destinatario' => $id_destinatario,
            ':nombre_original' => $nombre_original,
            ':ruta_archivo'    => $ruta_archivo,
            ':descripcion'     => $descripcion !== '' ? mb_substr($descripcion, 0, 500) : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function obtenerPorTutoria(int $id_tutoria): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT d.*,
                    o.nombre AS origen_nombre, o.apellido AS origen_apellido,
                    e.nombre AS destinatario_nombre, e.apellido AS destinatario_apellido
             FROM documentos_expediente d
             INNER JOIN usuarios o ON d.id_origen = o.id_usuario
             INNER JOIN usuarios e ON d.id_destinatario = e.id_usuario
             WHERE d.id_tutoria = :id_tutoria
             ORDER BY d.fecha DESC, d.id_documento DESC"
        );
        $stmt->execute([':id_tutoria' => $id_tutoria]);

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id_documento): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM documentos_expediente WHERE id_documento = :id");
        $stmt->execute([':id' => $id_documento]);
        $doc = $stmt->fetch();

        return $doc ?: null;
    }
}