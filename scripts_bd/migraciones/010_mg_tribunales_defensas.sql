USE tutorias_db;

-- =========================================================
-- TRIBUNALES, DEFENSAS Y CALIFICACIONES
-- =========================================================
-- Este módulo conserva el historial de cambios.
-- Los tribunales y defensas anteriores nunca se eliminan.
-- =========================================================


-- =========================================================
-- 1. Tribunales asignados a cada expediente y etapa
-- =========================================================

CREATE TABLE IF NOT EXISTS tribunales_defensa (
    id_tribunal INT AUTO_INCREMENT PRIMARY KEY,

    id_expediente INT NOT NULL,
    etapa ENUM(
        'mg1',
        'mg2'
    ) NOT NULL,

    -- Los tribunales son docentes registrados como tutores
    id_tutor INT NOT NULL,

    -- Permite identificar Tribunal 1 y Tribunal 2
    orden TINYINT UNSIGNED NOT NULL,

    fecha_asignacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_fin DATETIME NULL,

    estado ENUM(
        'vigente',
        'reemplazado'
    ) NOT NULL DEFAULT 'vigente',

    motivo_cambio VARCHAR(255) NULL,

    registrado_por INT NOT NULL,

    -- Vale 1 mientras la asignación esté vigente.
    -- Al reemplazarla pasa a NULL para conservar el historial.
    asignacion_vigente TINYINT NULL DEFAULT 1,

    CONSTRAINT fk_tribunal_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_tribunal_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_tribunal_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Impide dos tribunales vigentes en la misma posición
    CONSTRAINT uq_tribunal_orden_vigente
        UNIQUE (
            id_expediente,
            etapa,
            orden,
            asignacion_vigente
        ),

    -- Impide asignar dos veces al mismo docente en una etapa
    CONSTRAINT uq_tribunal_docente_vigente
        UNIQUE (
            id_expediente,
            etapa,
            id_tutor,
            asignacion_vigente
        ),

    CONSTRAINT chk_tribunal_orden
        CHECK (orden BETWEEN 1 AND 10),

    CONSTRAINT chk_tribunal_vigente
        CHECK (
            asignacion_vigente IS NULL
            OR asignacion_vigente = 1
        ),

    INDEX idx_tribunal_expediente (
        id_expediente,
        etapa
    ),

    INDEX idx_tribunal_tutor (
        id_tutor
    ),

    INDEX idx_tribunal_estado (
        estado
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 2. Defensas programadas
-- =========================================================

CREATE TABLE IF NOT EXISTS defensas_mg (
    id_defensa INT AUTO_INCREMENT PRIMARY KEY,

    id_expediente INT NOT NULL,

    etapa ENUM(
        'mg1',
        'mg2'
    ) NOT NULL,

    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,

    ambiente VARCHAR(100) NOT NULL,

    estado ENUM(
        'programada',
        'realizada',
        'reprogramada',
        'cancelada'
    ) NOT NULL DEFAULT 'programada',

    observaciones_fondo TEXT NULL,
    observaciones_forma TEXT NULL,

    motivo_cambio VARCHAR(255) NULL,

    -- Las excepciones son autorizadas fuera del sistema.
    -- Aquí solamente guardamos su respaldo.
    autorizado_por VARCHAR(150) NULL,
    referencia_autorizacion VARCHAR(100) NULL,

    registrado_por INT NOT NULL,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    -- Solo puede existir una defensa actual por etapa.
    -- Las reprogramadas o canceladas pasan a NULL.
    defensa_vigente TINYINT NULL DEFAULT 1,

    CONSTRAINT fk_defensa_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_defensa_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_defensa_vigente
        UNIQUE (
            id_expediente,
            etapa,
            defensa_vigente
        ),

    CONSTRAINT chk_defensa_horas
        CHECK (hora_fin > hora_inicio),

    CONSTRAINT chk_defensa_vigente
        CHECK (
            defensa_vigente IS NULL
            OR defensa_vigente = 1
        ),

    INDEX idx_defensa_fecha (
        fecha,
        hora_inicio,
        hora_fin
    ),

    INDEX idx_defensa_expediente (
        id_expediente,
        etapa
    ),

    INDEX idx_defensa_estado (
        estado
    ),

    INDEX idx_defensa_ambiente (
        ambiente,
        fecha
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;


-- =========================================================
-- 3. Calificaciones registradas por defensa
-- =========================================================

CREATE TABLE IF NOT EXISTS calificaciones_mg (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,

    id_defensa INT NOT NULL,

    nota DECIMAL(5,2) NOT NULL,

    observaciones VARCHAR(500) NULL,

    -- El estudiante solamente podrá verla cuando se publique.
    publicada TINYINT(1) NOT NULL DEFAULT 0,

    registrada_por INT NOT NULL,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_calificacion_defensa
        FOREIGN KEY (id_defensa)
        REFERENCES defensas_mg(id_defensa)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_calificacion_registrador
        FOREIGN KEY (registrada_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    -- Cada defensa posee una sola calificación general.
    CONSTRAINT uq_calificacion_defensa
        UNIQUE (id_defensa),

    CONSTRAINT chk_calificacion_publicada
        CHECK (publicada IN (0, 1)),

    INDEX idx_calificacion_publicada (
        publicada
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;