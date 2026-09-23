# Sistema de Tutorías UPDS — Proyecto fusionado

## Qué contiene

Este proyecto integra el trabajo funcional de `tecnologiasweb` con la identidad visual del proyecto base del ingeniero. El backend conserva la lógica MVC y la conexión existente; la interfaz adopta el tema institucional UPDS.

## Arquitectura

- `controllers/`: flujo de solicitudes, CRUD, permisos y acciones.
- `models/`: acceso a datos y reglas de negocio.
- `views/`: interfaz institucional responsive.
- `includes/`: sesión, CSRF, validaciones, roles, flash y auditoría.
- `database/init.sql`: esquema completo reconstruido.
- `assets/`: tema visual, logos y recursos del proyecto base.

## Dos modalidades de tutoría

### Materias — grupal

Las sesiones se basan en horarios institucionales de lunes a viernes. Los tres turnos oficiales son: 07:00–10:00, 15:00–18:00 y 19:00–22:00. El estudiante se inscribe en una sesión publicada; no elige ni altera el bloque.

### Fin de carrera — personal

La solicitud se vincula con un proyecto de grado y un tutor. El horario se valida contra la disponibilidad real del tutor y contra conflictos del estudiante/tutor.

## Conexión

`config/conexion.php` se conserva sin cambios respecto al proyecto avanzado. No se creó una conexión paralela.

## Primera ejecución con Docker

Debido a que el `.init` fue reconstruido, para una instalación limpia se recomienda eliminar el volumen de MySQL del proyecto y volver a levantar los servicios:

```bash
docker compose down -v
docker compose up --build -d
```

Después, abre:

- Web: `http://localhost:8000`
- phpMyAdmin: `http://localhost:8080`

> La aplicación utiliza las variables de conexión que ya estaban configuradas en el proyecto (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).

## Credenciales de prueba

- Administradores: `admin1`, `admin2`, `admin3` / `password`
- Tutores: `tutor1` a `tutor6` / `tutor`
- Estudiantes: `estudiante1` a `estudiante12` / `estudiante`

La base de datos de `database/init.sql` ya incluye datos de prueba para probar estados pendientes, aprobados, rechazados, programados, en curso, realizados, cancelados y concluidos.

## Rutas principales

- `/controllers/dashboard.php` — panel administrativo.
- `/views/tutor/panel.php` — panel del tutor.
- `/views/estudiante/panel.php` — panel del estudiante.
- `/controllers/tutorias_grupales_listar.php` — tutorías de materias.
- `/controllers/tutorias_personales_listar.php` — tutorías de fin de carrera.
- `/controllers/tutorias_solicitar.php` — selector de modalidad para estudiantes.
- `/controllers/mis_tutorias.php` — agenda consolidada personal.
- `/controllers/horarios_grupales_listar.php` — horarios oficiales de materias.
- `/controllers/auditoria_listar.php` — trazabilidad administrativa.

## Validación realizada en el workspace

- Sintaxis PHP: todos los archivos `.php` pasan `php -l`.
- Rutas de controladores referenciadas por la interfaz: no se detectaron destinos inexistentes.
- Las tablas nuevas usadas por tutorías grupales, proyectos y notificaciones están declaradas en el `.init`.
- `config/conexion.php` tiene el mismo hash SHA-256 que el archivo del proyecto avanzado.

La prueba de levantar contenedores debe ejecutarse en el equipo local donde esté instalado Docker; este workspace no dispone del binario Docker.

## Actualización funcional — solicitudes de tutorías grupales

Las inscripciones de estudiantes a tutorías grupales siguen ahora este flujo:

1. El estudiante solicita una plaza y la participación queda en `pendiente`.
2. Mientras está pendiente, el estudiante puede usar `Retirarme`.
3. Administración revisa la solicitud desde la ficha de participantes y utiliza `Aprobar`.
4. Al aprobarse, la participación pasa a `aprobada`, deja de mostrar `Retirarme` y se incorpora al total de participantes.
5. Las tutorías aprobadas aparecen en la agenda del estudiante; las solicitudes pendientes no se consideran sesiones programadas para el estudiante.

Para probar una versión actualizada, recrear la base desde cero usando el `database/init.sql` definitivo. No se requieren migraciones separadas.
