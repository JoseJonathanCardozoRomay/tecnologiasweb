# Sistema de Tutorías Académicas — Nota de base de datos

En el proyecto fusionado, `database/init.sql` es la **fuente única y definitiva** para crear la base de datos.

No se utilizan migraciones incrementales para las correcciones del proyecto. Cuando el modelo de datos cambia, se actualiza directamente `database/init.sql` para que una instalación limpia reproduzca el esquema correcto.

Para recrear la base desde cero en Docker:

```bash
docker compose down -v
docker compose up --build -d
```

El archivo `config/conexion.php` se mantiene separado y no se modifica como parte de la reconstrucción del esquema.
