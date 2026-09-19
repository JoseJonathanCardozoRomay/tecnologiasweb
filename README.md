# Sistema de Tutorías Académicas

Las migraciones de `database/migrations/` son para bases de datos ya creadas. El archivo `database/init.sql` solo se ejecuta automáticamente cuando el volumen de MySQL está vacío.

`database/init.sql` es la fuente oficial de inicialización de la base de datos. El archivo `docs/legacy/sistema_tutorias.sql` se conserva únicamente como referencia histórica y está obsoleto.

Para habilitar aprobación de cuentas nuevas, `REGISTRO_REQUIERE_APROBACION` usa `1` por defecto. `REGISTRO_DOMINIO_PERMITIDO` permanece vacío por defecto y puede limitar el dominio de correo.
