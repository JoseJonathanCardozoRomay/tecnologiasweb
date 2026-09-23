# Registro de correcciones

## Corrección — Tutorías grupales: alta y cancelación
- El botón administrativo **Programar sesión** ahora se presenta como **Agregar tutoría** con el estilo compacto del resto del sistema.
- Al cancelar una tutoría grupal, el campo de motivo ya no aparece permanentemente en la tabla: el administrador pulsa **Cancelar** y el motivo se solicita mediante un cuadro de diálogo.
- El motivo continúa siendo obligatorio (5–500 caracteres) y se envía al mismo flujo seguro de cancelación.
- En **Nueva tutoría grupal** se separaron las selecciones de **Materia**, **Docente/Tutor** y **Horario**, manteniendo la validación contra los horarios institucionales activos.
- El backend resuelve la combinación seleccionada al `id_horario` real antes de crear la sesión, evitando duplicar la relación en la base de datos.
- No se modificó la base de datos ni se creó ninguna migración.


## Corrección 1 — Paneles y codificación

- Se eliminó el botón grande y redundante **Solicitar tutoría** del encabezado del panel del estudiante; la acción permanece en **Acciones rápidas** y en la navegación **Tutorías**.
- Se eliminaron las acciones duplicadas del encabezado del panel del tutor; los accesos permanecen en **Accesos rápidos** y en la navegación lateral.
- Se eliminaron las acciones globales redundantes del encabezado del panel administrativo; la navegación lateral y los accesos rápidos siguen concentrando esas operaciones.
- Se añadió una corrección defensiva de texto mojibake en la salida HTML para cadenas como `TecnologÃ­a`, sin cambiar la conexión ni la estructura de la base de datos.
- Los datos semilla del `database/init.sql` ya estaban escritos correctamente como UTF-8; la corrección anterior protege además instalaciones que conserven datos antiguos con codificación dañada.


## Corrección 2 — flujo de aprobación de tutorías grupales

- Las solicitudes de estudiantes ahora se registran como `pendiente`.
- Mientras una solicitud está pendiente, el estudiante conserva `Retirarme`.
- Administración dispone de la acción `Aprobar`.
- Una solicitud aprobada pasa a `aprobada` y deja de mostrar `Retirarme`.
- Las inscripciones aprobadas son las únicas que cuentan como participantes activos en métricas y listados.
- Se actualizó el dashboard del estudiante para contabilizar solicitudes grupales pendientes y aprobadas.
- Se agregó notificación interna cuando administración aprueba una solicitud.
- Se añadió `database/init.sql`.

- Se agregó validación de conflictos de horario antes de aprobar una solicitud grupal.


## Corrección siguiente — Flujo de tutorías personales

- Se eliminó el botón superior redundante de solicitud de tutoría personal.
- El flujo de tutorías personales quedó definido como: estudiante solicita → pendiente → tutor acepta/rechaza → administración confirma → programada.
- El estudiante no puede cancelar tutorías personales.
- La cancelación de tutorías personales corresponde exclusivamente al administrador.
- El tutor solo puede aceptar/rechazar solicitudes y finalizar sesiones ya programadas.
- La base de datos definitiva de esta versión se concentra en `database/init.sql`; no se agregan migraciones incrementales.

## Corrección acumulativa — disponibilidad de tutorías personales

- Se eliminó del formulario del estudiante la captura de `Aula o enlace virtual`; la ubicación definitiva ahora la asigna administración al confirmar.
- Se incorporó una consulta específica de disponibilidad personal que muestra los próximos 28 días con horas libres de una hora.
- Las horas disponibles se calculan sobre los bloques de disponibilidad semanal del tutor y descuentan las solicitudes/tutorías que ya ocupan ese horario.
- Una tutoría personal dura exactamente 1 hora y la validación del servidor lo exige también frente a intentos de manipulación del formulario.
- El estudiante selecciona día y hora desde los espacios libres mostrados; ya no introduce manualmente fecha/hora inicio/fin.
- El listado administrativo permite confirmar la tutoría después de la aceptación del tutor y asignar modalidad + aula/enlace mediante un modal.
- El estudiante y el tutor pueden ver el aula/enlace únicamente cuando la tutoría ya está programada.
- Se mantiene `database/init.sql` sin nuevas migraciones; esta corrección no requiere cambios de estructura de base de datos.

## Corrección 03 — Permisos y vista de estudiantes para tutorías grupales

- La cancelación de tutorías grupales queda restringida al administrador tanto en la interfaz como en el controlador.
- El tutor conserva únicamente las acciones propias de su ciclo académico: iniciar y finalizar sesiones.
- En la pantalla `tutorias_grupales_estudiantes.php`, el tutor ya no visualiza el distintivo de estado de la sesión en la esquina superior derecha.
- El administrador conserva la visualización del estado y la acción de cancelación.
- No se modificó la base de datos; no se creó ninguna migración.

## Corrección — tutorías personales y aceptación exclusiva
- El tutor puede aceptar o rechazar una solicitud personal, pero no puede cancelarla.
- Cuando un tutor acepta una solicitud de fin de carrera, el sistema conserva esa solicitud en estado general `pendiente` hasta la confirmación administrativa.
- La aceptación se procesa dentro de una transacción con bloqueo de las solicitudes pendientes del mismo estudiante y proyecto.
- Al aceptar un tutor, se eliminan automáticamente las demás solicitudes pendientes enviadas a otros tutores para ese mismo proyecto.
- Se evita así que dos tutores puedan aceptar simultáneamente la misma solicitud de acompañamiento.
- No se creó ninguna migración ni fue necesario modificar `database/init.sql` para esta corrección.

## Corrección — agenda separada por modalidad y cantidad de estudiantes

- `Mis tutorías` ya no mezcla en una sola tabla las tutorías grupales y las personales.
- Se muestran dos bloques independientes: **Tutorías de materias** y **Tutorías de fin de carrera**.
- La agenda de cada modalidad se ordena y limita de forma independiente para mantener visibles ambas secciones.
- En el bloque de tutorías grupales del tutor se muestra explícitamente la **cantidad de estudiantes inscritos/aprobados** de cada sesión.
- El contador reutiliza `total_estudiantes`, por lo que no se altera el modelo de datos ni se crea una migración.

### Corrección visual — Mi disponibilidad
- Se ajustó el botón **Nuevo horario** a un tamaño compacto (`btn-sm`) para mantener la jerarquía visual y la estética consistente del sistema, conservando la misma acción y funcionalidad.
### Corrección visual — formulario de disponibilidad
- Se simplificó la selección de `Inicio` y `Fin` mediante listas desplegables en intervalos de 15 minutos.
- Se eliminó la necesidad de introducir manualmente la hora y los segundos.
- Los horarios existentes con minutos fuera del intervalo se conservan al editarse.
- No se modificó la estructura de la base de datos ni se agregó ninguna migración.

## Corrección — botón Nuevo estudiante
- Se mantiene la acción **Nuevo estudiante**, pero se reduce su tamaño y se unifica su presentación con los botones compactos del sistema.
- Se aplica la clase reutilizable `page-header-compact-action` para evitar un bloque visual sobredimensionado en el encabezado del listado.
- No se modificó la base de datos ni las conexiones.


## Corrección — Horarios grupales por turnos y búsqueda
- Se compactó visualmente el botón **Nuevo horario** para alinearlo con los demás encabezados.
- Se separaron los horarios en tres bloques visibles: **Mañana**, **Tarde** y **Noche**.
- Se agregó búsqueda por nombre de **materia** junto al filtro de estado.
- Se incorporó botón **Limpiar** cuando existen filtros activos.
- Se mantiene la visibilidad restringida por rol y la filtración de estados existente.

## 2026-09-23 — Unificación de Proyectos de grado

- Se eliminaron de la navegación visible las secciones separadas de "Fin de carrera".
- "Proyectos de grado" ahora es el módulo único que reúne proyectos y solicitudes de tutoría personal.
- La creación de proyectos quedó restringida al estudiante; administración no puede crear proyectos.
- Las solicitudes personales se consultan desde una pestaña del mismo módulo y conservan el flujo tutor → administración → programación.
- Administración confirma la solicitud aceptada por el tutor y asigna modalidad + aula/enlace desde el mismo módulo.
- Las rutas históricas de tutorías personales redirigen al módulo unificado para evitar accesos duplicados.
- No se modificó la base de datos ni la conexión existente.

## 2026-09-23 — Integración de Proyectos de grado y tutorías personales

- Se consolidó la navegación en una sola sección visible: "Proyectos de grado".
- La misma pantalla reúne los proyectos y las solicitudes de tutoría personal mediante pestañas.
- Se eliminó la posibilidad de crear un proyecto desde el rol administrador.
- La solicitud personal sigue el flujo tutor → administración, y administración puede confirmar y asignar aula/enlace desde el módulo unificado.
- Las rutas históricas de tutorías personales redirigen a la sección unificada.
- No se modificó la base de datos ni la conexión existente.

### Corrección — Evaluaciones separadas por docente
- El listado de evaluaciones ahora se presenta en bloques independientes por tutor/docente.
- Cada bloque muestra el historial y la cantidad de evaluaciones correspondientes a ese docente.
- Se mantiene la información académica, calificación, comentario y fecha dentro de cada bloque.
- Se ajustó el orden de consulta para agrupar por docente y ordenar sus evaluaciones por fecha descendente.
- No requiere cambios en la base de datos ni migraciones.

## Auditoría — exportación Excel

- Se ajustaron los botones **Imprimir** y **Descargar Excel** para usar el estilo compacto del proyecto.
- La descarga de auditoría dejó de generar TXT y ahora genera un archivo **`.xlsx`** compatible con Excel.
- La exportación mantiene los filtros de fecha y descarga todos los registros, sin el límite de 1000 aplicado a la vista web.
- Se agregó un generador XLSX interno (`includes/excel_exportador.php`) sin dependencias Composer.
- Docker instala la extensión PHP `zip`, requerida para empaquetar el archivo XLSX.

## Corrección — estado del proyecto tras defensa
- El estado visible `en_proceso` de los proyectos se presenta como **En curso**.
- Administración puede registrar el resultado de la defensa únicamente cuando el proyecto está En curso.
- Si la defensa es aprobada, el proyecto pasa al estado interno `finalizado`, mostrado como **Concluido**.
- Un proyecto Concluido ya no admite edición, nuevas solicitudes de tutoría ni eliminación.
- Si la defensa es reprobada, el proyecto permanece **En curso** y el estudiante puede continuar modificándolo y solicitando tutorías cuando no exista una sesión activa.
- Para evitar inconsistencias, no se permite concluir un proyecto que aún tenga tutorías personales pendientes, programadas o en ejecución.
- El resultado queda registrado en auditoría.
- No se modificó la estructura de la base de datos ni se creó una migración.

### Corrección — Estado Propuesto y aprobación dual de proyectos
- Se conserva `propuesto` en el filtro de Proyectos de grado.
- El proyecto permanece `propuesto` mientras la solicitud personal todavía no haya completado la aceptación del tutor y la confirmación administrativa.
- Al confirmar administración una tutoría personal aceptada por el tutor, el proyecto pasa automáticamente a `en_proceso` (visible como “En curso”).
- No se creó ninguna migración ni se modificó la conexión; la lógica se sincroniza en la operación de confirmación existente.

## Disponibilidad de bloques al crear horarios grupales
- El selector de bloque ahora muestra únicamente los turnos oficiales libres del tutor en el día elegido.
- La interfaz se actualiza al cambiar tutor o día sin recargar la página.
- El servidor vuelve a validar la disponibilidad para evitar selecciones obsoletas o manipuladas.
- En edición se excluye el propio horario para conservar su bloque actual.
- No se modificó la base de datos ni se creó una migración.
## Corrección global de codificación de nombres
- Se reemplazó el uso de `utf8_decode()` por una reparación segura mediante `iconv` para cadenas UTF-8 que fueron interpretadas como Windows-1252.
- La corrección se centraliza en `repararMojibake()` y se aplica mediante `e()` y los avatares Unicode, cubriendo nombres, apellidos, tutores, estudiantes, carreras y materias mostrados por las vistas.
- Se actualizó también la generación de Excel para reutilizar el mismo reparador en textos exportados.
- Se verificó el proyecto completo y no quedaron literales de mojibake fuera de la documentación del reparador.
