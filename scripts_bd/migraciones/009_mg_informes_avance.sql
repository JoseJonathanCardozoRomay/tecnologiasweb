USE tutorias_db;

-- =========================================================
-- Informes de avance de Modalidades de Grado
-- =========================================================

CREATE TABLE IF NOT EXISTS informes_avance_mg (
    id_informe INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente INT NOT NULL,
    id_hito INT NULL,

    etapa ENUM(
        'mg1',
        'mg2'
    ) NOT NULL,

    numero_informe TINYINT UNSIGNED NOT NULL,
    fecha_informe DATE NOT NULL,
    porcentaje_avance TINYINT UNSIGNED NOT NULL DEFAULT 0,

    resumen_avance TEXT NOT NULL,
    logros TEXT NULL,
    dificultades TEXT NULL,
    recomendaciones TEXT NULL,
    proximas_actividades TEXT NULL,

    estado ENUM(
        'borrador',
        'presentado',
        'aprobado',
        'observado'
    ) NOT NULL DEFAULT 'borrador',

    observacion_revision VARCHAR(500) NULL,
    revisado_por INT NULL,
    fecha_revision DATETIME NULL,

    registrado_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_informes_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_informes_hito
        FOREIGN KEY (id_hito)
        REFERENCES calendario_mg(id_hito)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT fk_informes_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_informes_revisor
        FOREIGN KEY (revisado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT uq_informe_expediente_etapa
        UNIQUE (
            id_expediente,
            etapa,
            numero_informe
        ),

    CONSTRAINT chk_informe_numero
        CHECK (
            numero_informe BETWEEN 1 AND 20
        ),

    CONSTRAINT chk_informe_avance
        CHECK (
            porcentaje_avance BETWEEN 0 AND 100
        ),

    INDEX idx_informes_expediente (
        id_expediente
    ),

    INDEX idx_informes_etapa (
        etapa
    ),

    INDEX idx_informes_estado (
        estado
    ),

    INDEX idx_informes_fecha (
        fecha_informe
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;