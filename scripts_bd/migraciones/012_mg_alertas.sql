USE tutorias_db;

-- =========================================================
-- ALERTAS ACADÉMICAS ATENDIDAS
-- =========================================================
-- Las alertas se calculan mediante consultas.
-- Esta tabla registra cuándo Coordinación atiende una alerta.
-- =========================================================

CREATE TABLE IF NOT EXISTS alertas_atendidas (
    id_alerta_atendida INT AUTO_INCREMENT PRIMARY KEY,

    tipo_alerta VARCHAR(60) NOT NULL,
    id_referencia INT NOT NULL,

    -- Identificador estable formado por tipo y referencia.
    clave_alerta VARCHAR(150) NOT NULL,

    nota VARCHAR(500) NOT NULL,

    atendida_por INT NOT NULL,

    fecha_atencion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_alerta_clave
        UNIQUE (clave_alerta),

    CONSTRAINT fk_alerta_usuario
        FOREIGN KEY (atendida_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_alerta_tipo (
        tipo_alerta
    ),

    INDEX idx_alerta_referencia (
        id_referencia
    ),

    INDEX idx_alerta_fecha (
        fecha_atencion
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;