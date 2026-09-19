# Sistema de Tutorías Académicas

Las migraciones de `database/migrations/` son para bases de datos ya creadas. El archivo `database/init.sql` solo se ejecuta automáticamente cuando el volumen de MySQL está vacío.

`database/init.sql` es la fuente oficial de inicialización de la base de datos. El archivo `docs/legacy/sistema_tutorias.sql` se conserva únicamente como referencia histórica y está obsoleto.

Para habilitar aprobación de cuentas nuevas, `REGISTRO_REQUIERE_APROBACION` usa `1` por defecto. `REGISTRO_DOMINIO_PERMITIDO` permanece vacío por defecto y puede limitar el dominio de correo.

## Migraciones de base de datos
Después de hacer `git pull`, ejecuta:

```
docker compose exec web php scripts/migrar.php
```

El runner `scripts/migrar.php` aplica solo las migraciones pendientes de `database/migrations/` y registra cada una en la tabla `schema_migrations`, por lo que es seguro ejecutarlo varias veces.

- Si la base ya existía antes de agregar el control de migraciones (caso de las migraciones `001`, `002` y `003`, que ya estaban aplicadas o son manuales), regístralas sin ejecutarlas una sola vez con:

  ```
  docker compose exec web php scripts/migrar.php --marcar-aplicadas
  ```
