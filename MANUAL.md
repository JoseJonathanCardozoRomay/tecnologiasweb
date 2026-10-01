# Manual de Usuario: Autenticación y Control de Acceso

Este documento detalla el procedimiento para ingresar al sistema de tutorías académicas, especificando las credenciales requeridas, el flujo de acceso y la matriz de permisos correspondiente a cada rol dentro de la plataforma.

---

## 1. Proceso de Inicio de Sesión (Login)

Para ingresar a la plataforma, siga los pasos que se describen a continuación:

1. **Acceso al Formulario:** Abra su navegador web e ingrese a la dirección URL del sistema (por ejemplo, `http://localhost/` o la URL del dominio en producción).
2. **Ingreso de Credenciales:**
   * En el campo **Correo Electrónico / Usuario**, ingrese su dirección institucional o nombre de usuario registrado.
   * En el campo **Contraseña**, ingrese su clave de acceso.
3. **Validación:** Presione el botón **Iniciar Sesión**.
4. **Redirección:** El sistema validará sus credenciales mediante una sesión segura en el servidor y lo redirigirá automáticamente al **Panel Principal (Dashboard)** que corresponda a su perfil.

> [!NOTE]
> Si las credenciales son incorrectas, el sistema desplegará un mensaje de advertencia indicando que el correo o la contraseña no coinciden con los registros.

---

## 2. Matriz de Permisos por Rol

El sistema utiliza un modelo de control de acceso basado en roles (RBAC) para garantizar la seguridad de la información y restringir las acciones según el tipo de usuario.

### Estudiante
Es el usuario destinatario del servicio de tutorías.

- **Ver Perfil:** Consultar y actualizar sus datos personales básicos.
- **Buscar Tutores:** Explorar la lista de tutores disponibles según materia, área o disponibilidad.
- **Solicitar Tutoría:** Agendar o solicitar sesiones de acompañamiento académico.
- **Historial de Sesiones:** Revisar el historial de tutorías solicitadas, asignadas y completadas.
- **Calificación y Feedback:** Emitir valoraciones sobre las tutorías recibidas.

---

### Tutor / Docente
Es el usuario encargado de brindar el acompañamiento pedagógico a los estudiantes.

- **Gestión de Disponibilidad:** Configurar sus horarios y materias disponibles para impartir tutorías.
- **Aceptación / Rechazo:** Aprobar o rechazar las solicitudes de tutoría enviadas por los estudiantes.
- **Registro de Bitácora:** Registrar observaciones, asistencia y notas al finalizar cada sesión de tutoría.
- **Consulta de Agenda:** Visualizar el calendario de tutorías programadas y pendientes.

---

### Administrador
Es el rol de gestión global encargado de la supervisión y mantenimiento operativo del sistema.

- **Gestión de Usuarios:** Crear, editar, activar, suspender o eliminar cuentas de estudiantes, tutores y administradores.
- **Asignación de Roles y Materias:** Mapear tutores con las materias que están habilitados para impartir.
- **Monitoreo Global:** Visualizar todas las tutorías agendadas, en curso y finalizadas dentro de la institución.
- **Generación de Reportes:** Extraer estadísticas institucionales sobre el programa de tutorías.
- **Configuración del Sistema:** Administrar parámetros generales del sistema.

---

## 3. Seguridad y Buenas Prácticas

- **Cierre de Sesión:** Siempre haga clic en la opción **Cerrar Sesión** en la esquina superior derecha antes de salir, especialmente si utiliza un equipo compartido.
- **Confidencialidad:** Las credenciales de acceso son personales e intransferibles. Los administradores nunca le solicitarán su contraseña por ningún medio.