USE tutorias_db;

-- =========================================================
-- Separación del módulo de Tutorías y Modalidades de Grado
-- =========================================================
-- Modalidades de Grado utilizará expedientes_mg y sus
-- propias tablas. La tabla tutorias vuelve a representar
-- exclusivamente sesiones académicas por materia.
-- =========================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS eliminar_fk_si_existe$$

CREATE PROCEDURE eliminar_fk_si_existe(
    IN nombre_tabla VARCHAR(64),
    IN nombre_fk VARCHAR(64)
)
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = nombre_tabla
            AND CONSTRAINT_NAME = nombre_fk
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ) THEN
        SET @sql_fk = CONCAT(
            'ALTER TABLE `',
            nombre_tabla,
            '` DROP FOREIGN KEY `',
            nombre_fk,
            '`'
        );

        PREPARE sentencia_fk FROM @sql_fk;
        EXECUTE sentencia_fk;
        DEALLOCATE PREPARE sentencia_fk;
    END IF;
END$$

DROP PROCEDURE IF EXISTS eliminar_columna_si_existe$$

CREATE PROCEDURE eliminar_columna_si_existe(
    IN nombre_tabla VARCHAR(64),
    IN nombre_columna VARCHAR(64)
)
BEGIN
    IF EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = nombre_tabla
            AND COLUMN_NAME = nombre_columna
    ) THEN
        SET @sql_columna = CONCAT(
            'ALTER TABLE `',
            nombre_tabla,
            '` DROP COLUMN `',
            nombre_columna,
            '`'
        );

        PREPARE sentencia_columna FROM @sql_columna;
        EXECUTE sentencia_columna;
        DEALLOCATE PREPARE sentencia_columna;
    END IF;
END$$

DELIMITER ;

-- =========================================================
-- 1. Eliminamos relaciones agregadas para el diseño anterior
-- =========================================================

CALL eliminar_fk_si_existe(
    'tutorias',
    'fk_tutorias_modalidad'
);

CALL eliminar_fk_si_existe(
    'tutorias',
    'fk_tutorias_periodo'
);

CALL eliminar_fk_si_existe(
    'tutorias',
    'fk_tutorias_tutor_sugerido'
);

CALL eliminar_fk_si_existe(
    'tutorias',
    'fk_tutorias_creador'
);

-- =========================================================
-- 2. Eliminamos solamente las columnas provisionales de MG
-- =========================================================

CALL eliminar_columna_si_existe(
    'tutorias',
    'id_modalidad'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'id_periodo'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'etapa'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'id_tutor_sugerido'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'titulo_trabajo'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'descripcion_tematica'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'creado_por'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'fecha_cierre'
);

CALL eliminar_columna_si_existe(
    'tutorias',
    'motivo_cancelacion'
);

-- =========================================================
-- 3. Restauramos los campos de las tutorías académicas
-- =========================================================
-- id_tutor tiene una clave foránea, por eso se retira
-- temporalmente antes de modificar la columna.

CALL eliminar_fk_si_existe(
    'tutorias',
    'fk_tutorias_tutor'
);

ALTER TABLE tutorias
    MODIFY id_tutor INT NOT NULL,
    MODIFY fecha DATE NOT NULL,
    MODIFY hora_inicio TIME NOT NULL,
    MODIFY hora_fin TIME NOT NULL,
    MODIFY modalidad ENUM(
        'presencial',
        'virtual'
    ) NOT NULL DEFAULT 'presencial',
    MODIFY estado ENUM(
        'pendiente',
        'confirmada',
        'realizada',
        'cancelada'
    ) NOT NULL DEFAULT 'pendiente';

-- Restauramos la relación original con la tabla tutores.

ALTER TABLE tutorias
    ADD CONSTRAINT fk_tutorias_tutor
        FOREIGN KEY (id_tutor)
        REFERENCES tutores(id_tutor)
        ON UPDATE CASCADE;

-- =========================================================
-- 4. Retiramos los procedimientos temporales
-- =========================================================

DROP PROCEDURE IF EXISTS eliminar_fk_si_existe;
DROP PROCEDURE IF EXISTS eliminar_columna_si_existe;