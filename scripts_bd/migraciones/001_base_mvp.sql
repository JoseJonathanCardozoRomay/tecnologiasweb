USE tutorias_db;

-- =========================================================
-- 1. Modalidades oficiales de graduación
-- =========================================================

CREATE TABLE IF NOT EXISTS modalidades_graduacion (
    id_modalidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    activa TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2. Reglas según modalidad y etapa académica
-- =========================================================

CREATE TABLE IF NOT EXISTS reglas_modalidad_etapa (
    id_regla INT AUTO_INCREMENT PRIMARY KEY,
    id_modalidad INT NOT NULL,
    etapa ENUM('grado_1', 'grado_2') NOT NULL,
    minimo_reuniones_semana TINYINT UNSIGNED NOT NULL DEFAULT 0,
    cantidad_informes TINYINT UNSIGNED NOT NULL DEFAULT 0,

    CONSTRAINT fk_reglas_modalidad
        FOREIGN KEY (id_modalidad)
        REFERENCES modalidades_graduacion(id_modalidad)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT uq_regla_modalidad_etapa
        UNIQUE (id_modalidad, etapa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3. Periodos habilitados para incorporar estudiantes
-- =========================================================

CREATE TABLE IF NOT EXISTS periodos_inscripcion (
    id_periodo INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM(
        'planificado',
        'abierto',
        'cerrado'
    ) NOT NULL DEFAULT 'planificado',
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_periodo_fechas
        CHECK (fecha_fin >= fecha_inicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4. Cupo asignado a cada tutor dentro de un periodo
-- =========================================================

CREATE TABLE IF NOT EXISTS tutor_periodo (
    id_tutor_periodo INT AUTO_INCREMENT PRIMARY KEY,
    id_tutor INT NOT NULL,
    id_periodo INT NOT NULL,
    cupo_maximo TINYINT UNSIGNED NOT NULL DEFAULT 5,
    activo TINYINT(1) NOT NULL DEFAULT 1,

    CONSTRAINT fk_tutor_periodo_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_tutor_periodo_periodo
        FOREIGN KEY (id_periodo)
        REFERENCES periodos_inscripcion(id_periodo)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT uq_tutor_periodo
        UNIQUE (id_tutor, id_periodo),

    CONSTRAINT chk_tutor_periodo_cupo
        CHECK (cupo_maximo BETWEEN 1 AND 50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5. Datos iniciales de modalidades oficiales
-- =========================================================

INSERT IGNORE INTO modalidades_graduacion (
    nombre,
    descripcion
)
VALUES
(
    'Proyecto',
    'Desarrollo de una propuesta o solución aplicada.'
),
(
    'Tesis',
    'Trabajo de investigación académica.'
),
(
    'Trabajo Dirigido',
    'Trabajo desarrollado dentro de una institución.'
);

-- =========================================================
-- 6. Reglas iniciales configurables
-- =========================================================

INSERT IGNORE INTO reglas_modalidad_etapa (
    id_modalidad,
    etapa,
    minimo_reuniones_semana,
    cantidad_informes
)
SELECT
    id_modalidad,
    'grado_1',
    1,
    0
FROM modalidades_graduacion;

INSERT IGNORE INTO reglas_modalidad_etapa (
    id_modalidad,
    etapa,
    minimo_reuniones_semana,
    cantidad_informes
)
SELECT
    id_modalidad,
    'grado_2',
    0,
    4
FROM modalidades_graduacion;