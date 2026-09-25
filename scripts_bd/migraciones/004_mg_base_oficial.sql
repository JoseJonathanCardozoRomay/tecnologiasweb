USE tutorias_db;

-- =========================================================
-- HU-020, HU-021 y HU-022
-- Base oficial del módulo Modalidades de Grado
-- =========================================================

-- =========================================================
-- 1. Roles del equipo de Modalidades de Grado
-- =========================================================

INSERT IGNORE INTO roles (
    nombre_rol
)
VALUES
    ('coordinador_mg'),
    ('auxiliar_mg');

-- =========================================================
-- 2. Parámetros configurables
-- =========================================================

CREATE TABLE IF NOT EXISTS parametros_mg (
    clave VARCHAR(60) PRIMARY KEY,
    valor VARCHAR(100) NULL,
    descripcion VARCHAR(255) NOT NULL,
    fuente VARCHAR(100) NOT NULL,
    estado_evidencia ENUM(
        'confirmado',
        'pendiente',
        'propuesta'
    ) NOT NULL,
    actualizado_por INT NULL,
    fecha_actualizacion DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_parametros_mg_usuario
        FOREIGN KEY (actualizado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- Las cifras pendientes se almacenan como parámetros.
-- No se utilizan como restricciones obligatorias.

INSERT IGNORE INTO parametros_mg (
    clave,
    valor,
    descripcion,
    fuente,
    estado_evidencia
)
VALUES
(
    'reuniones_min_semana_perfil',
    '2',
    'Cantidad mínima recomendada de reuniones semanales durante MG1.',
    'ENT-03',
    'confirmado'
),
(
    'dias_alerta_sin_reunion',
    '10',
    'Días sin reuniones antes de mostrar una alerta.',
    'Plan de implementación v1',
    'propuesta'
),
(
    'tutor_carga_recomendada',
    '3',
    'Carga recomendada de estudiantes vigentes por tutor.',
    'ENT-03',
    'confirmado'
),
(
    'tutor_max_estudiantes',
    NULL,
    'Máximo formal de estudiantes por tutor. No genera bloqueo mientras permanezca pendiente.',
    'C-01',
    'pendiente'
),
(
    'dias_anticipacion_tribunal',
    '14',
    'Anticipación aproximada para asignar tribunales.',
    'ENT-03',
    'confirmado'
),
(
    'tribunales_por_defensa_mg1',
    '2',
    'Cantidad de tribunales para la defensa de MG1.',
    'ENT-03',
    'confirmado'
),
(
    'tribunales_por_defensa_mg2',
    '2',
    'Cantidad provisional de tribunales para MG2.',
    'C-02',
    'pendiente'
),
(
    'min_interesados_examen',
    '12',
    'Cantidad mencionada para habilitar Examen de Grado.',
    'Normativa pendiente',
    'pendiente'
),
(
    'promedio_excelencia',
    '90',
    'Promedio mencionado para Graduación por Excelencia.',
    'Normativa pendiente',
    'pendiente'
),
(
    'duracion_mg1_meses',
    '2',
    'Duración aproximada de MG1.',
    'ENT-03',
    'confirmado'
),
(
    'duracion_mg2_meses',
    '4',
    'Duración aproximada de MG2.',
    'ENT-03',
    'confirmado'
),
(
    'plazo_registro_reunion_dias',
    '7',
    'Días permitidos para registrar una reunión anterior.',
    'Plan de implementación v1',
    'propuesta'
);

-- =========================================================
-- 3. Modalidades oficiales de grado
-- =========================================================

CREATE TABLE IF NOT EXISTS modalidades_grado (
    id_modalidad INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    requiere_tutor TINYINT(1) NOT NULL DEFAULT 0,
    flujo ENUM(
        'perfil_mg',
        'examen_areas',
        'excelencia'
    ) NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_modalidad_requiere_tutor
        CHECK (requiere_tutor IN (0, 1)),

    CONSTRAINT chk_modalidad_activa
        CHECK (activa IN (0, 1))
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO modalidades_grado (
    codigo,
    nombre,
    descripcion,
    requiere_tutor,
    flujo
)
VALUES
(
    'EXCELENCIA',
    'Graduación por Excelencia',
    'Modalidad sujeta a la normativa académica oficial.',
    0,
    'excelencia'
),
(
    'PROYECTO_GRADO',
    'Proyecto de Grado',
    'Desarrollo de una propuesta o solución aplicada.',
    1,
    'perfil_mg'
),
(
    'TESIS',
    'Tesis',
    'Trabajo de investigación académica.',
    1,
    'perfil_mg'
),
(
    'EXAMEN_GRADO',
    'Examen de Grado',
    'Evaluación individual organizada por áreas.',
    0,
    'examen_areas'
),
(
    'TRABAJO_DIRIGIDO',
    'Trabajo Dirigido',
    'Trabajo desarrollado dentro de una institución.',
    1,
    'perfil_mg'
);

-- =========================================================
-- 4. Cohortes de ingreso
-- =========================================================

CREATE TABLE IF NOT EXISTS cohortes_mg (
    id_cohorte INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_cohorte_fechas
        CHECK (
            fecha_fin IS NULL
            OR fecha_fin >= fecha_inicio
        ),

    CONSTRAINT chk_cohorte_activa
        CHECK (activa IN (0, 1)),

    INDEX idx_cohortes_mg_activa (
        activa
    ),

    INDEX idx_cohortes_mg_fechas (
        fecha_inicio,
        fecha_fin
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. Calendario de hitos por cohorte
-- =========================================================

CREATE TABLE IF NOT EXISTS calendario_mg (
    id_hito INT AUTO_INCREMENT PRIMARY KEY,
    id_cohorte INT NOT NULL,
    etapa ENUM(
        'previa',
        'mg1',
        'mg2'
    ) NOT NULL,
    tipo ENUM(
        'taller',
        'asignacion_tutor',
        'asignacion_tribunal',
        'informe',
        'defensa',
        'ingreso_mg2',
        'otro'
    ) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    orden SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    fecha_limite DATE NOT NULL,
    avance_esperado_pct TINYINT UNSIGNED NULL,
    fecha_registro DATETIME NOT NULL
        DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_calendario_mg_cohorte
        FOREIGN KEY (id_cohorte)
        REFERENCES cohortes_mg(id_cohorte)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT chk_calendario_avance
        CHECK (
            avance_esperado_pct IS NULL
            OR avance_esperado_pct BETWEEN 0 AND 100
        ),

    CONSTRAINT uq_calendario_mg_orden
        UNIQUE (
            id_cohorte,
            etapa,
            orden
        ),

    INDEX idx_calendario_mg_cohorte (
        id_cohorte
    ),

    INDEX idx_calendario_mg_fecha (
        fecha_limite
    ),

    INDEX idx_calendario_mg_tipo (
        tipo
    )
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;