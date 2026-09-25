USE tutorias_db;

-- =========================================================
-- Adaptación de tutorías al nuevo proceso de graduación
-- =========================================================

ALTER TABLE tutorias

    -- El proceso puede quedar temporalmente sin tutor
    -- mientras coordinación realiza una reasignación.
    MODIFY id_tutor INT NULL,

    -- Los horarios específicos estarán en reuniones.
    -- Se conservan estos campos para registros anteriores.
    MODIFY fecha DATE NULL,
    MODIFY hora_inicio TIME NULL,
    MODIFY hora_fin TIME NULL,

    MODIFY modalidad ENUM(
        'presencial',
        'virtual'
    ) NULL DEFAULT NULL,

    -- Ampliamos los estados sin eliminar los anteriores.
    MODIFY estado ENUM(
        'pendiente',
        'confirmada',
        'asignada',
        'en_proceso',
        'en_reasignacion',
        'detenido',
        'realizada',
        'finalizada',
        'cancelada'
    ) NOT NULL DEFAULT 'pendiente',

    -- Modalidad oficial del trabajo de graduación.
    ADD COLUMN id_modalidad INT NULL
        AFTER id_materia,

    -- Periodo en el que se incorpora al estudiante.
    ADD COLUMN id_periodo INT NULL
        AFTER id_modalidad,

    -- Etapa académica del proceso.
    ADD COLUMN etapa ENUM(
        'grado_1',
        'grado_2'
    ) NULL
        AFTER id_periodo,

    -- Tutor sugerido por el estudiante.
    -- La decisión final continúa siendo de coordinación.
    ADD COLUMN id_tutor_sugerido INT NULL
        AFTER id_tutor,

    -- Información principal del trabajo.
    ADD COLUMN titulo_trabajo VARCHAR(200) NULL
        AFTER etapa,

    ADD COLUMN descripcion_tematica TEXT NULL
        AFTER titulo_trabajo,

    -- Usuario de coordinación que creó el proceso.
    ADD COLUMN creado_por INT NULL
        AFTER descripcion_tematica,

    -- Información utilizada al cerrar o cancelar el proceso.
    ADD COLUMN fecha_cierre DATETIME NULL
        AFTER fecha_solicitud,

    ADD COLUMN motivo_cancelacion VARCHAR(255) NULL
        AFTER fecha_cierre,

    -- Relaciones nuevas.
    ADD CONSTRAINT fk_tutorias_modalidad
        FOREIGN KEY (id_modalidad)
        REFERENCES modalidades_graduacion(id_modalidad)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    ADD CONSTRAINT fk_tutorias_periodo
        FOREIGN KEY (id_periodo)
        REFERENCES periodos_inscripcion(id_periodo)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    ADD CONSTRAINT fk_tutorias_tutor_sugerido
        FOREIGN KEY (id_tutor_sugerido)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    ADD CONSTRAINT fk_tutorias_creador
        FOREIGN KEY (creado_por)
        REFERENCES usuarios(id_usuario)
        ON UPDATE CASCADE
        ON DELETE SET NULL,

    ADD INDEX idx_tutorias_modalidad (id_modalidad),
    ADD INDEX idx_tutorias_periodo (id_periodo),
    ADD INDEX idx_tutorias_tutor_sugerido (id_tutor_sugerido),
    ADD INDEX idx_tutorias_etapa (etapa);