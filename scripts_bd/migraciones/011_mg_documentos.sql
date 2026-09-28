USE tutorias_db;

-- =========================================================
-- PLANTILLAS Y DOCUMENTOS GENERADOS
-- =========================================================


-- =========================================================
-- 1. Plantillas editables
-- =========================================================

CREATE TABLE IF NOT EXISTS plantillas_documento (
    id_plantilla INT AUTO_INCREMENT PRIMARY KEY,

    codigo VARCHAR(60) NOT NULL,
    nombre VARCHAR(120) NOT NULL,

    cuerpo_html LONGTEXT NOT NULL,

    version SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    activa TINYINT(1) NOT NULL DEFAULT 1,

    actualizado_por INT NULL,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT uq_plantilla_codigo
        UNIQUE (codigo),

    CONSTRAINT fk_plantilla_actualizador
        FOREIGN KEY (actualizado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT chk_plantilla_version
        CHECK (version >= 1),

    CONSTRAINT chk_plantilla_activa
        CHECK (activa IN (0, 1)),

    INDEX idx_plantilla_activa (
        activa
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 2. Correlativos anuales por tipo de documento
-- =========================================================

CREATE TABLE IF NOT EXISTS contadores_documento (
    tipo VARCHAR(60) NOT NULL,
    anio SMALLINT UNSIGNED NOT NULL,
    ultimo_numero INT UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY (
        tipo,
        anio
    ),

    CONSTRAINT chk_contador_numero
        CHECK (ultimo_numero >= 0)
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 3. Documentos emitidos
-- =========================================================

CREATE TABLE IF NOT EXISTS documentos_generados (
    id_documento INT AUTO_INCREMENT PRIMARY KEY,

    id_plantilla INT NOT NULL,
    id_expediente INT NOT NULL,
    id_defensa INT NULL,

    tipo VARCHAR(60) NOT NULL,

    destinatario VARCHAR(200) NOT NULL,

    numero_correlativo VARCHAR(60) NOT NULL,

    -- Conservamos exactamente el contenido que fue emitido.
    contenido_snapshot LONGTEXT NOT NULL,

    generado_por INT NOT NULL,

    fecha_generacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_documento_plantilla
        FOREIGN KEY (id_plantilla)
        REFERENCES plantillas_documento(id_plantilla)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_documento_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_documento_defensa
        FOREIGN KEY (id_defensa)
        REFERENCES defensas_mg(id_defensa)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_documento_generador
        FOREIGN KEY (generado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_documento_correlativo
        UNIQUE (numero_correlativo),

    INDEX idx_documento_expediente (
        id_expediente
    ),

    INDEX idx_documento_defensa (
        id_defensa
    ),

    INDEX idx_documento_tipo (
        tipo
    ),

    INDEX idx_documento_fecha (
        fecha_generacion
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 4. Plantillas provisionales de citación
-- =========================================================

INSERT IGNORE INTO plantillas_documento (
    codigo,
    nombre,
    cuerpo_html
)
VALUES
(
    'CITACION_TRIBUNAL',
    'Citación para tribunal de defensa',
    '<h1>Citación a defensa</h1>
    <p><strong>[PLANTILLA PROVISIONAL]</strong></p>
    <p>Se comunica a {{destinatario}} que ha sido designado como Tribunal {{orden_tribunal}} de la defensa correspondiente a:</p>
    <p><strong>Estudiante:</strong> {{estudiante_nombre}}</p>
    <p><strong>Modalidad:</strong> {{modalidad}}</p>
    <p><strong>Trabajo:</strong> {{titulo_trabajo}}</p>
    <p><strong>Etapa:</strong> {{etapa}}</p>
    <p><strong>Fecha:</strong> {{fecha_defensa}}</p>
    <p><strong>Horario:</strong> {{horario}}</p>
    <p><strong>Ambiente:</strong> {{ambiente}}</p>
    <p><strong>Número:</strong> {{numero_documento}}</p>'
),
(
    'CITACION_ESTUDIANTE',
    'Citación para estudiante',
    '<h1>Citación a defensa</h1>
    <p><strong>[PLANTILLA PROVISIONAL]</strong></p>
    <p>Se comunica a {{destinatario}} la programación de su defensa académica.</p>
    <p><strong>Modalidad:</strong> {{modalidad}}</p>
    <p><strong>Trabajo:</strong> {{titulo_trabajo}}</p>
    <p><strong>Etapa:</strong> {{etapa}}</p>
    <p><strong>Fecha:</strong> {{fecha_defensa}}</p>
    <p><strong>Horario:</strong> {{horario}}</p>
    <p><strong>Ambiente:</strong> {{ambiente}}</p>
    <p><strong>Número:</strong> {{numero_documento}}</p>'
);