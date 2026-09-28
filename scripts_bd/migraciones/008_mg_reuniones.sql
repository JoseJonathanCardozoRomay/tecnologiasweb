USE tutorias_db;

-- =========================================================
-- 1. Reuniones de seguimiento
-- =========================================================

CREATE TABLE IF NOT EXISTS reuniones_mg (
    id_reunion INT AUTO_INCREMENT PRIMARY KEY,
    id_asignacion INT NOT NULL,

    fecha_reunion DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,

    modalidad ENUM(
        'presencial',
        'virtual'
    ) NOT NULL,

    lugar_enlace VARCHAR(255) NULL,
    tema VARCHAR(150) NOT NULL,
    acuerdos TEXT NULL,
    observaciones TEXT NULL,

    estado ENUM(
        'programada',
        'realizada',
        'cancelada'
    ) NOT NULL DEFAULT 'programada',

    asistencia_tutor ENUM(
        'pendiente',
        'presente',
        'ausente',
        'justificada'
    ) NOT NULL DEFAULT 'pendiente',

    asistencia_estudiante ENUM(
        'pendiente',
        'presente',
        'ausente',
        'justificada'
    ) NOT NULL DEFAULT 'pendiente',

    estado_validacion ENUM(
        'pendiente',
        'validada',
        'observada'
    ) NOT NULL DEFAULT 'pendiente',

    observacion_validacion VARCHAR(255) NULL,
    validada_por INT NULL,
    fecha_validacion DATETIME NULL,

    registrado_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_reuniones_asignacion
        FOREIGN KEY (id_asignacion)
        REFERENCES asignaciones_tutor(id_asignacion)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_reuniones_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_reuniones_validador
        FOREIGN KEY (validada_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT chk_reunion_horario
        CHECK (hora_fin > hora_inicio),

    INDEX idx_reuniones_asignacion (
        id_asignacion
    ),

    INDEX idx_reuniones_fecha (
        fecha_reunion
    ),

    INDEX idx_reuniones_estado (
        estado
    ),

    INDEX idx_reuniones_validacion (
        estado_validacion
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. Evidencias adjuntas a cada reunión
-- =========================================================

CREATE TABLE IF NOT EXISTS evidencias_reunion_mg (
    id_evidencia INT AUTO_INCREMENT PRIMARY KEY,
    id_reunion INT NOT NULL,

    tipo ENUM(
        'acta',
        'fotografia',
        'documento',
        'enlace',
        'otro'
    ) NOT NULL DEFAULT 'documento',

    nombre_original VARCHAR(255) NOT NULL,
    ruta_archivo VARCHAR(255) NOT NULL,
    tipo_mime VARCHAR(100) NULL,
    tamano_bytes INT UNSIGNED NULL,
    hash_archivo CHAR(64) NULL,
    descripcion VARCHAR(255) NULL,

    subido_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_evidencias_reunion
        FOREIGN KEY (id_reunion)
        REFERENCES reuniones_mg(id_reunion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_evidencias_usuario
        FOREIGN KEY (subido_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_evidencias_reunion (
        id_reunion
    ),

    INDEX idx_evidencias_tipo (
        tipo
    ),

    INDEX idx_evidencias_hash (
        hash_archivo
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;