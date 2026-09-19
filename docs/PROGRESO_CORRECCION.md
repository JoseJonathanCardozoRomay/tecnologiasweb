# Progreso de la corrección — error crítico Fase 4

Fecha: corrección del error `Table 'tutorias_db.seguimiento_sesion' doesn't exist`.

## Causa raíz
El código de la Fase 4 (seguimiento de sesiones, notificaciones y `motivo_cancelacion`)
está aplicado en el código, pero la base que usa la aplicación **no tiene** la tabla
`seguimiento_sesion` (ni `notificaciones`, ni la columna `tutorias.motivo_cancelacion`).
`models/TutoriaModel.php::obtenerReportePorPeriodo()` consulta `seguimiento_sesion`, y por
eso `controllers/reportes.php` (línea 14) lanza el `PDOException` 42S02.

**Por qué persiste aunque se aplicó "a mano" en phpMyAdmin** (hipótesis, a confirmar con el
Paso 1 de comandos):
1. phpMyAdmin ejecuta sentencia por sentencia y **no corre bien el bloque
   `PREPARE/EXECUTE`**, o se aplicó sin seleccionar la base `tutorias_db`.
2. Se aplicó sobre **otra base/servidor** (phpMyAdmin abierto contra un MySQL local, no
   contra el contenedor `db`). Según `docker-compose.yml` y `.env`, `web` (`DB_HOST=db`),
   `db` y `phpmyadmin` (`PMA_HOST=db`) apuntan al mismo contenedor/base; hay que confirmarlo.
3. La página mostraba un error **en caché** de una ejecución anterior.

### Cambios aplicados para dejarlo funcionando de raíz
- Se **quitó `USE tutorias_db;`** de la migración 004: la base se selecciona al invocar
  `mysql ... tutorias_db` o vía la conexión PDO, evitando que apunte a otra base.
- `scripts/migrar.php` ahora **divide el archivo en sentencias** y las ejecuta una a una
  (no depende del multi-statement de PDO, que es lo que suele fallar en phpMyAdmin/exec),
  e **ignora errores "ya existe"** (1050/1060/1061) para ser re-ejecutable sin riesgo.

## Qué se hizo (código)
1. `database/migrations/004_seguimiento_notificaciones.sql` se hizo **idempotente**: ahora
   comprueba `information_schema.COLUMNS` antes de ejecutar el `ALTER TABLE ... ADD COLUMN`
   (los dos `CREATE TABLE` ya usaban `IF NOT EXISTS`). Así puede re-ejecutarse sin error.
2. `database/init.sql` ya contiene las mismas tablas y columna (verificado), de modo que
   una base nueva queda igual que una migrada.
3. Se creó `scripts/migrar.php`: runner CLI que registra migraciones en `schema_migrations`,
   aplica solo las pendientes en orden alfabético y soporta `--marcar-aplicadas`.
4. Se documentó el runner en `README.md`.
5. `.gitignore` excluye `.env`, `backup_antes_migracion.sql` y `*.sql.bak`.

## Qué falta (lo que debes ejecutar tú)
Este entorno **no tiene Docker, PHP, mysql ni mysqldump**, así que los pasos que requieren
la base en ejecución no se pudieron ejecutar aquí. Siguiente paso exacto, en orden:

1. Respaldo (no se sube a git gracias al `.gitignore`):
   ```
   docker compose exec db sh -c 'mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" tutorias_db' > backup_antes_migracion.sql
   ```
2. Ver el estado actual de la base:
   ```
   docker compose exec db mysql -uroot -p<clave> tutorias_db -e "SHOW TABLES; SHOW COLUMNS FROM tutorias;"
   ```
3. Aplicar la migración 004 (idempotente):
   ```
   docker compose exec -T db mysql -uroot -p<clave> tutorias_db < database/migrations/004_seguimiento_notificaciones.sql
   ```
4. Registrar el control de migraciones y marcar las antiguas:
   ```
   docker compose exec web php scripts/migrar.php --marcar-aplicadas
   docker compose exec web php scripts/migrar.php
   ```
   La segunda ejecución debe decir: "No hay migraciones pendientes. La base de datos está al día."
5. Verificación de pantallas por rol (ver informe).

## Problema pendiente
No verificado con ejecución real (R2): el entorno del agente **no tiene Docker, PHP ni mysql**.
El punto crítico a confirmar es que el contenedor `web` y phpMyAdmin ven la MISMA base
`tutorias_db` del contenedor `db` (comandos del Paso 1c/1d del informe).

Si tras ejecutar el runner por terminal las pantallas siguen fallando, NO seguir con
phpMyAdmin: usar el runner (`docker compose exec web php scripts/migrar.php`), que es el
camino fiable y registra el estado en `schema_migrations`.
