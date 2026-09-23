# Auditoría y mapa de fusión — Sistema de Tutorías UPDS

## 1. Diagnóstico de `tecnologiasweb`

- Arquitectura PHP MVC ligera: `controllers/`, `models/`, `views/`, `includes/`, `config/`, `database/`.
- 61 controladores, 14 modelos y 40 vistas en la versión auditada.
- Fortalezas conservadas: CRUD académicos, roles/permisos, disponibilidad de tutores, proyectos de grado, tutorías personales, tutorías grupales, asistencia, evaluaciones, auditoría, validaciones y Docker.
- Conexión existente conservada sin cambios: `config/conexion.php`, Dockerfile y variables de conexión de `docker-compose.yml`.
- Se verificó sintaxis PHP en los archivos existentes: sin errores de sintaxis.

## 2. Diagnóstico del proyecto base

- Arquitectura PHP MVC ligera similar, con una capa visual mucho más elaborada.
- Aporta el tema institucional `assets/css/upds-theme.css`, logos, Bootstrap 5, Bootstrap Icons, SweetAlert2 y Chart.js.
- El header/base visual tiene sidebar responsive, navegación por rol, cards KPI, tablas, formularios, estados y layout consistente.
- Se reutilizan estos componentes como referencia visual principal, sin copiar ciegamente sus módulos funcionales duplicados.

## 3. Conflictos encontrados

### Base de datos
La implementación avanzada ya utiliza `proyectos_grado`, `horarios_tutoria_grupal`, `tutorias_grupales` y `tutoria_grupal_estudiante`, pero esas tablas no estaban presentes en el `database/init.sql` original. El `.init` fusionado las incorpora.

### Perfil del estudiante
`EstudianteModel::obtenerPorUsuario()` devolvía solo las columnas de `estudiantes`, mientras la vista necesitaba `nombre_carrera`. La versión fusionada relaciona estudiante con carrera y entrega esa información directamente.

### Tutoría personal
La ruta de tutoría personal invocaba `TutoriaModel::validarTutoriaPersonal()` aunque el método no existía. La versión fusionada implementa esa validación y enlaza la sesión con `proyectos_grado`.

### Navegación
El proyecto avanzado utilizaba un navbar horizontal genérico; el proyecto base utiliza un layout institucional con sidebar y navegación responsive. La navegación final adopta el segundo enfoque y agrega menús de Tutorías separados por tipo.

## 4. Mapa de fusión

### Se conserva de `tecnologiasweb`
- Lógica de negocio de tutorías.
- CRUD y validaciones académicas.
- Roles y autorización.
- Disponibilidad de tutores.
- Proyectos de grado.
- Tutorías grupales y gestión de participantes/asistencia.
- Auditoría y cancelaciones.
- Docker y conexión existente.

### Se adopta del proyecto base
- Identidad visual UPDS.
- Header/sidebar responsive.
- Cards, KPI, formularios y tablas.
- Bootstrap/SweetAlert2/Chart.js.
- Componentes visuales y parciales reutilizables.

### Se combina
- Backend avanzado + presentación institucional.
- Reportes y dashboard con datos de tutorías personales y grupales.
- Toasts para operaciones y badges para estados.
- Tom Select en formularios estratégicos sin reemplazar indiscriminadamente todos los selects existentes.

## 5. Modelo de datos final

La base fusionada incluye:

`roles`, `usuarios`, `registro_intentos`, `carreras`, `estudiantes`, `tutores`, `materias`, `tutor_materia`, `disponibilidad_tutor`, `periodos_tutoria`, `bloques_horarios`, `proyectos_grado`, `tutorias`, `evaluaciones_tutoria`, `seguimiento_sesion`, `notificaciones`, `registro_accesos`, `horarios_tutoria_grupal`, `tutorias_grupales`, `tutoria_grupal_estudiante`.

Las tutorías grupales utilizan únicamente los bloques institucionales de 3 horas:

- Mañana: 07:00–10:00
- Tarde: 15:00–18:00
- Noche: 19:00–22:00

Las tutorías personales de fin de carrera utilizan la disponibilidad real del tutor y pueden estar enlazadas a un proyecto/tesis.

## 6. Reglas de acceso

- Administrador: gestión y consulta institucional, programación y supervisión.
- Tutor: consulta/gestión de su agenda, materias, disponibilidad y estudiantes asignados.
- Estudiante: perfil, carrera, solicitud de tutorías personales y participación en tutorías grupales; solo puede operar sobre sus propias solicitudes/inscripciones.

## 7. Pruebas realizadas sobre la fusión

- Validación de sintaxis PHP con `php -l`.
- Validación del árbol de archivos y ausencia de conflictos de rutas críticas.
- Validación estática de referencias a las nuevas tablas del esquema.
- Revisión de la configuración Docker y verificación del hash de `config/conexion.php`; el entorno de ejecución disponible no incluye el binario Docker, por lo que la creación efectiva de contenedores queda pendiente de ejecutar en el equipo local.
