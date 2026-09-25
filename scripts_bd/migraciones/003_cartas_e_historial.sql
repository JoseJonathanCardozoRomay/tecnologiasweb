USE tutorias_db;

-- =========================================================
-- 1. Cartas de designación
-- =========================================================

CREATE TABLE cartas_designacion (
    id_carta INT AUTO_INCREMENT PRIMARY KEY,
    id_tutoria INT NOT NULL,
    id_tutor INT NOT NULL,
    numero_carta VARCHAR(50) NULL,
    version_carta SMALLINT UNSIGNED NOT NULL DEFAULT 1,

    fecha_generacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME NULL,

    estado ENUM(
        'pendiente',
        'aceptada',
        'rechazada',
        'anulada'
    ) NOT NULL DEFAULT 'pendiente',

    tipo_rechazo ENUM(
        'sin_capacidad',
        'sin_tiempo',
        'no_corresponde',
        'otro'
    ) NULL,

    motivo_rechazo VARCHAR(255) NULL,
    firma_recepcion VARCHAR(200) NULL,
    responsabilidades TEXT NULL,
    documento_url VARCHAR(255) NULL,
    vigente TINYINT(1) NOT NULL DEFAULT 1,
    creado_por INT NOT NULL,

    CONSTRAINT fk_cartas_tutoria
        FOREIGN KEY (id_tutoria)
        REFERENCES tutorias(id_tutoria)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_cartas_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_cartas_creador
        FOREIGN KEY (creado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_carta_tutoria_version
        UNIQUE (id_tutoria, version_carta),

    CONSTRAINT uq_carta_numero
        UNIQUE (numero_carta),

    CONSTRAINT chk_carta_version
        CHECK (version_carta >= 1),

    CONSTRAINT chk_carta_vigente
        CHECK (vigente IN (0, 1)),

    INDEX idx_cartas_tutor (id_tutor),
    INDEX idx_cartas_estado (estado),
    INDEX idx_cartas_vigente (id_tutoria, vigente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. Historial de tutores que participaron en el proceso
-- =========================================================

CREATE TABLE historial_tutores (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_tutoria INT NOT NULL,
    id_tutor INT NOT NULL,
    id_carta INT NULL,

    fecha_inicio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_fin DATETIME NULL,

    estado ENUM(
        'propuesto',
        'activo',
        'rechazado',
        'renuncio',
        'reasignado',
        'finalizado'
    ) NOT NULL DEFAULT 'propuesto',

    motivo_cambio VARCHAR(255) NULL,

    CONSTRAINT fk_historial_tutoria
        FOREIGN KEY (id_tutoria)
        REFERENCES tutorias(id_tutoria)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_historial_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_historial_carta
        FOREIGN KEY (id_carta)
        REFERENCES cartas_designacion(id_carta)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_historial_tutoria (id_tutoria),
    INDEX idx_historial_tutor (id_tutor),
    INDEX idx_historial_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;