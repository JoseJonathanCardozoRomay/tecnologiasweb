USE tutorias_db;

-- =========================================================
-- 1. Expedientes de Modalidades de Grado
-- =========================================================

CREATE TABLE IF NOT EXISTS expedientes_mg (
    id_expediente INT AUTO_INCREMENT PRIMARY KEY,
    id_estudiante INT NOT NULL,
    id_modalidad INT NOT NULL,
    id_cohorte INT NOT NULL,

    etapa_actual ENUM(
        'previa',
        'mg1',
        'mg2',
        'finalizado'
    ) NOT NULL DEFAULT 'previa',

    estado ENUM(
        'activo',
        'aprobado',
        'reprobado',
        'abandono',
        'retirado'
    ) NOT NULL DEFAULT 'activo',

    titulo_trabajo VARCHAR(200) NULL,
    fecha_inicio DATE NOT NULL,
    fecha_cierre DATE NULL,
    observaciones TEXT NULL,

    creado_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_expedientes_estudiante
        FOREIGN KEY (id_estudiante)
        REFERENCES estudiantes(id_estudiante)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_expedientes_modalidad
        FOREIGN KEY (id_modalidad)
        REFERENCES modalidades_grado(id_modalidad)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_expedientes_cohorte
        FOREIGN KEY (id_cohorte)
        REFERENCES cohortes_mg(id_cohorte)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_expedientes_creador
        FOREIGN KEY (creado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT uq_expediente_proceso
        UNIQUE (
            id_estudiante,
            id_modalidad,
            id_cohorte
        ),

    CONSTRAINT chk_expediente_fechas
        CHECK (
            fecha_cierre IS NULL
            OR fecha_cierre >= fecha_inicio
        ),

    INDEX idx_expedientes_modalidad (
        id_modalidad
    ),

    INDEX idx_expedientes_cohorte (
        id_cohorte
    ),

    INDEX idx_expedientes_etapa (
        etapa_actual
    ),

    INDEX idx_expedientes_estado (
        estado
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. Historial de etapas de cada expediente
-- =========================================================

CREATE TABLE IF NOT EXISTS expediente_etapas (
    id_expediente_etapa INT AUTO_INCREMENT PRIMARY KEY,
    id_expediente INT NOT NULL,

    etapa ENUM(
        'previa',
        'mg1',
        'mg2'
    ) NOT NULL,

    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,

    resultado VARCHAR(30) NULL,
    motivo_cierre VARCHAR(255) NULL,

    registrado_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_etapas_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_etapas_registrador
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_etapas_fechas
        CHECK (
            fecha_fin IS NULL
            OR fecha_fin >= fecha_inicio
        ),

    INDEX idx_etapas_expediente (
        id_expediente
    ),

    INDEX idx_etapas_etapa (
        etapa
    ),

    INDEX idx_etapas_abiertas (
        id_expediente,
        fecha_fin
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3. Cabecera de importaciones desde CSV
-- =========================================================

CREATE TABLE IF NOT EXISTS importaciones_mg (
    id_importacion INT AUTO_INCREMENT PRIMARY KEY,

    nombre_archivo VARCHAR(255) NOT NULL,
    hash_archivo CHAR(64) NOT NULL,

    estado ENUM(
        'previsualizada',
        'procesando',
        'completada',
        'fallida'
    ) NOT NULL DEFAULT 'previsualizada',

    total_filas INT UNSIGNED NOT NULL DEFAULT 0,
    filas_correctas INT UNSIGNED NOT NULL DEFAULT 0,
    filas_advertencia INT UNSIGNED NOT NULL DEFAULT 0,
    filas_error INT UNSIGNED NOT NULL DEFAULT 0,
    filas_creadas INT UNSIGNED NOT NULL DEFAULT 0,

    registrado_por INT NOT NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    fecha_procesamiento DATETIME NULL,

    CONSTRAINT fk_importaciones_usuario
        FOREIGN KEY (registrado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    INDEX idx_importaciones_hash (
        hash_archivo
    ),

    INDEX idx_importaciones_estado (
        estado
    ),

    INDEX idx_importaciones_fecha (
        fecha_registro
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. Resultado de cada fila importada
-- =========================================================

CREATE TABLE IF NOT EXISTS importaciones_mg_detalle (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_importacion INT NOT NULL,
    numero_fila INT UNSIGNED NOT NULL,

    registro_universitario VARCHAR(30) NULL,
    nombres VARCHAR(100) NULL,
    apellidos VARCHAR(100) NULL,
    correo VARCHAR(150) NULL,
    carrera VARCHAR(120) NULL,
    semestre VARCHAR(20) NULL,
    modalidad VARCHAR(80) NULL,
    cohorte VARCHAR(30) NULL,

    resultado ENUM(
        'correcta',
        'advertencia',
        'error',
        'pendiente_cuenta',
        'creada',
        'omitida'
    ) NOT NULL,

    mensaje VARCHAR(255) NULL,
    datos_originales JSON NULL,
    id_expediente INT NULL,

    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_importacion_detalle
        FOREIGN KEY (id_importacion)
        REFERENCES importaciones_mg(id_importacion)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_expediente
        FOREIGN KEY (id_expediente)
        REFERENCES expedientes_mg(id_expediente)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    CONSTRAINT uq_importacion_numero_fila
        UNIQUE (
            id_importacion,
            numero_fila
        ),

    INDEX idx_detalle_resultado (
        resultado
    ),

    INDEX idx_detalle_registro (
        registro_universitario
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;