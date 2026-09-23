# Datos de prueba incluidos

El `database/init.sql` deja la instalación lista para probar los tres roles y los principales estados del sistema.

## Cuentas

| Rol | Usuarios | Contraseña |
|---|---|---|
| Administrador | `admin1`, `admin2`, `admin3` | `password` |
| Tutor | `tutor1` ... `tutor6` | `tutor` |
| Estudiante | `estudiante1` ... `estudiante12` | `estudiante` |

## Datos académicos

Se cargó la carrera **Ingeniería de Sistemas** con las 54 materias organizadas según los 9 semestres entregados para la prueba. El esquema actual de `materias` conserva la materia y carrera; la organización por semestre se documenta en la carga y puede ampliarse en una futura versión si se decide persistir ese dato en la tabla.

También se incluyó una carrera inactiva para probar filtros administrativos.

## Estados de tutorías grupales

La carga contiene sesiones `programada`, `en_curso`, `realizada` y `cancelada`, además de participantes `pendiente`, `aprobada`, `asistio` y `no_asistio`.

## Estados de tutorías personales

Hay solicitudes `pendiente` con tutor pendiente, `pendiente` con tutor aceptado, `rechazada`, `programada`, `realizada` y `cancelada`.

## Proyectos de grado

Hay proyectos `propuesto`, `en_proceso`, `finalizado` y `cancelado`, además de tutorías y evaluaciones relacionadas.

## Pruebas recomendadas

1. Entrar como estudiante y revisar perfil, materias, tutorías grupales, proyectos y evaluaciones.
2. Entrar como tutor y revisar disponibilidad, estudiantes, aceptación/rechazo y finalización.
3. Entrar como administrador y revisar aprobaciones, programación, cancelaciones, proyectos, defensa, reportes y auditoría.


## Carga masiva adicional

La instalación limpia incluye una carga ampliada de más de 1.000 registros adicionales para pruebas. Además de las cuentas iniciales, se agregan los siguientes usuarios:

- Administradores: `admin4` ... `admin10` / `password`
- Tutores: `tutor7` ... `tutor31` / `tutor`
- Estudiantes: `estudiante13` ... `estudiante192` / `estudiante`

La carga ampliada incluye proyectos de grado, asignaciones tutor-materia, disponibilidades, horarios grupales, sesiones grupales, inscripciones, tutorías personales, evaluaciones, seguimientos, notificaciones, intentos de acceso y registros de auditoría.

La intención es que se puedan probar búsquedas y filtros con un volumen realista de información, además de los distintos estados del sistema.
