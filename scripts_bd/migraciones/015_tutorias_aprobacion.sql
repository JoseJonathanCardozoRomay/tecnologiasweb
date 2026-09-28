USE tutorias_db;

-- Aplica el flujo de aprobación sobre la base existente, sin borrar datos.
SET @sql_observacion = (
    SELECT IF(
        COUNT(*) = 0,
        'ALTER TABLE tutorias ADD COLUMN observacion_revision VARCHAR(500) NULL AFTER observaciones',
        'SELECT 1'
    )
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'tutorias'
        AND COLUMN_NAME = 'observacion_revision'
);

PREPARE sentencia_observacion FROM @sql_observacion;
EXECUTE sentencia_observacion;
DEALLOCATE PREPARE sentencia_observacion;

ALTER TABLE tutorias
    MODIFY fecha DATE NULL,
    MODIFY hora_inicio TIME NULL,
    MODIFY hora_fin TIME NULL,
    MODIFY modalidad ENUM('presencial', 'virtual') NULL DEFAULT NULL,
    MODIFY estado ENUM(
        'pendiente',
        'pendiente_tutor',
        'pendiente_aprobacion',
        'observada',
        'rechazada',
        'confirmada',
        'realizada',
        'cancelada'
    ) NOT NULL DEFAULT 'pendiente_tutor';
