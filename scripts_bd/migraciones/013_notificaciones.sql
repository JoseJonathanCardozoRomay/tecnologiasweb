USE tutorias_db;

-- =========================================================
-- NOTIFICACIONES INTERNAS
-- =========================================================

CREATE TABLE IF NOT EXISTS notificaciones (
    id_notificacion INT AUTO_INCREMENT PRIMARY KEY,

    id_usuario INT NOT NULL,

    tipo VARCHAR(60) NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensaje VARCHAR(500) NOT NULL,

    -- Solamente se guardarán rutas internas del sistema.
    url_destino VARCHAR(255) NULL,

    -- Evita generar dos veces el mismo aviso.
    clave_evento VARCHAR(180) NOT NULL,

    leida TINYINT(1) NOT NULL DEFAULT 0,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_lectura DATETIME NULL,

    CONSTRAINT fk_notificacion_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT uq_notificacion_evento_usuario
        UNIQUE (
            id_usuario,
            clave_evento
        ),

    CONSTRAINT chk_notificacion_leida
        CHECK (leida IN (0, 1)),

    INDEX idx_notificacion_usuario_leida (
        id_usuario,
        leida
    ),

    INDEX idx_notificacion_fecha (
        fecha_registro
    ),

    INDEX idx_notificacion_tipo (
        tipo
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;