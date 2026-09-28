USE tutorias_db;

-- =========================================================
-- 1. Historial de asignaciones de tutores
-- =========================================================

CREATE TABLE IF NOT EXISTS asignaciones_tutor (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente INT NOT NULL,
    id_tutor INT NOT NULL,

    fecha_asignacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_fin DATETIME NULL,

    estado ENUM(
        'vigente',
        'finalizada',
        'reemplazada'
    ) NOT NULL DEFAULT 'vigente',

    motivo_fin VARCHAR(255) NULL,
    referencia_decanatura VARCHAR(100) NULL,
    disponibilidad_consultada TINYINT(1) NOT NULL DEFAULT 0,
    fecha_nota_renuncia DATE NULL,
    registrado_por INT NOT NULL,

    -- El valor 1 identifica la asignación vigente.
    -- Cuando termina, esta columna debe cambiar a NULL.
    -- MySQL permite varios NULL, pero solamente un 1
    -- por expediente.
    asignacion_vigente TINYINT NULL DEFAULT 1,

    CONSTRAINT fk_asignaciones_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_asignaciones_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_asignaciones_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_asignacion_vigente
        UNIQUE (
            id_expediente,
            asignacion_vigente
        ),

    CONSTRAINT chk_asignacion_fechas
        CHECK (
            fecha_fin IS NULL
            OR fecha_fin >= fecha_asignacion
        ),

    CONSTRAINT chk_asignacion_estado
        CHECK (
            (
                estado = 'vigente'
                AND asignacion_vigente = 1
                AND fecha_fin IS NULL
            )
            OR
            (
                estado IN (
                    'finalizada',
                    'reemplazada'
                )
                AND asignacion_vigente IS NULL
                AND fecha_fin IS NOT NULL
            )
        ),

    INDEX idx_asignaciones_expediente (
        id_expediente
    ),

    INDEX idx_asignaciones_tutor (
        id_tutor
    ),

    INDEX idx_asignaciones_estado (
        estado
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. Cartas de designación del módulo MG
-- =========================================================

CREATE TABLE IF NOT EXISTS cartas_designacion_mg (
    id_carta INT AUTO_INCREMENT PRIMARY KEY,
    id_asignacion INT NOT NULL,

    numero_carta VARCHAR(50) NULL,
    version_carta SMALLINT UNSIGNED NOT NULL DEFAULT 1,

    estado ENUM(
        'pendiente',
        'aceptada',
        'rechazada',
        'anulada'
    ) NOT NULL DEFAULT 'pendiente',

    fecha_generacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_respuesta DATETIME NULL,

    tipo_rechazo ENUM(
        'sin_capacidad',
        'sin_tiempo',
        'no_corresponde',
        'otro'
    ) NULL,

    motivo_rechazo VARCHAR(255) NULL,
    responsabilidades TEXT NULL,
    documento_url VARCHAR(255) NULL,
    creado_por INT NOT NULL,

    CONSTRAINT fk_cartas_mg_asignacion
        FOREIGN KEY (id_asignacion)
        REFERENCES asignaciones_tutor(id_asignacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_cartas_mg_creador
        FOREIGN KEY (creado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_carta_mg_version
        UNIQUE (
            id_asignacion,
            version_carta
        ),

    CONSTRAINT uq_carta_mg_numero
        UNIQUE (numero_carta),

    CONSTRAINT chk_carta_mg_version
        CHECK (version_carta >= 1),

    INDEX idx_cartas_mg_estado (
        estado
    ),

    INDEX idx_cartas_mg_fecha (
        fecha_generacion
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
