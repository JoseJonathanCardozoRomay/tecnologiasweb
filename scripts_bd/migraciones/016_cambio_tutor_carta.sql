USE tutorias_db;

-- Aplica esta migración después de 011. No elimina asignaciones existentes.
SET @sql_ref = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE asignaciones_tutor ADD COLUMN referencia_decanatura VARCHAR(100) NULL',
        'SELECT 1')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'asignaciones_tutor'
        AND COLUMN_NAME = 'referencia_decanatura'
);
PREPARE migracion FROM @sql_ref;
EXECUTE migracion;
DEALLOCATE PREPARE migracion;

SET @sql_disponibilidad = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE asignaciones_tutor ADD COLUMN disponibilidad_consultada TINYINT(1) NOT NULL DEFAULT 0',
        'SELECT 1')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'asignaciones_tutor'
        AND COLUMN_NAME = 'disponibilidad_consultada'
);
PREPARE migracion FROM @sql_disponibilidad;
EXECUTE migracion;
DEALLOCATE PREPARE migracion;

SET @sql_fecha_nota = (
    SELECT IF(COUNT(*) = 0,
        'ALTER TABLE asignaciones_tutor ADD COLUMN fecha_nota_renuncia DATE NULL',
        'SELECT 1')
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'asignaciones_tutor'
        AND COLUMN_NAME = 'fecha_nota_renuncia'
);
PREPARE migracion FROM @sql_fecha_nota;
EXECUTE migracion;
DEALLOCATE PREPARE migracion;

INSERT IGNORE INTO plantillas_documento (codigo, nombre, cuerpo_html)
VALUES (
    'CARTA_TUTOR',
    'Carta de designación de tutor',
    '<h1>Carta de designación de tutor</h1>
    <p><strong>[PLANTILLA PROVISIONAL]</strong></p>
    <p>Número: {{numero_carta}}. Fecha: {{fecha_larga}}.</p>
    <p>Se comunica a {{tutor_nombre}} su designación como tutor de {{estudiante_nombre}}, registro universitario {{registro_universitario}}, de la carrera {{carrera}}.</p>
    <p>Modalidad: {{modalidad}}. Cohorte: {{cohorte}}.</p>
    <p>Tema: {{tema}}.</p>
    <p>Referencia de Decanatura: {{referencia_decanatura}}.</p>
    <hr>
    <p>Copia para el tutor y para el estudiante.</p>'
);
