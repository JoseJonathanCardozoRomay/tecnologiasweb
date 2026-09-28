USE tutorias_db;

-- =========================================================
-- BITÁCORA DE AUDITORÍA
-- =========================================================

CREATE TABLE IF NOT EXISTS bitacora_mg (
    id_bitacora BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_usuario INT NULL,

    accion VARCHAR(60) NOT NULL,
    tabla_afectada VARCHAR(80) NOT NULL,
    id_registro INT NULL,

    datos_antes JSON NULL,
    datos_despues JSON NULL,

    direccion_ip VARCHAR(45) NULL,
    agente_usuario VARCHAR(255) NULL,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bitacora_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    INDEX idx_bitacora_usuario (
        id_usuario
    ),

    INDEX idx_bitacora_accion (
        accion
    ),

    INDEX idx_bitacora_tabla (
        tabla_afectada
    ),

    INDEX idx_bitacora_registro (
        tabla_afectada,
        id_registro
    ),

    INDEX idx_bitacora_fecha (
        fecha_registro
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;