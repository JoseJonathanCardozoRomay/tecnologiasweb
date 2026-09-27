<?php
// =====================================================================
// SEEDER DE DATOS DE PRUEBA - Sistema de Tutorías Académicas UPDS
// ---------------------------------------------------------------------
//  Genera datos de ejemplo en TODAS las tablas del sistema (25 tablas).
//  Ejecutar dentro del contenedor web:
//      docker exec -it tutorias_web php /var/www/html/database/seed.php
//
//  REQUISITOS DE ESTE SEEDER:
//   * Todos los nombres, roles, descripciones y mensajes se escriben
//     SIN TILDES ni caracteres especiales (solo ASCII).
//   * Todos los usuarios se crean con la contraseña "Control123+"
//     (hash generado con password_hash() de PHP).
//   * Se insertan 20 o más filas en las entidades de datos. Única
//     excepción: la tabla `roles`, que es el catálogo fijo de
//     autorización (administrador, tutor, estudiante, auxiliar) sobre
//     el cual trabaja la lógica de permisos del sistema.
//
//  AVISO: este script BORRA (TRUNCATE) el contenido actual de la base
//  de datos antes de insertar los datos de prueba.
// =====================================================================

require_once __DIR__ . '/../config/conexion.php';

// ---------------------------------------------------------------
// 0. Limpieza total (reset de AUTO_INCREMENT incluido)
// ---------------------------------------------------------------
$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
$tablas = [
    'roles', 'usuarios', 'estudiantes', 'tutores', 'carreras', 'materias',
    'tutor_materia', 'docente_carreras', 'disponibilidad_tutor', 'tutorias',
    'tutoria_estudiantes', 'cartas_designacion', 'modalidades_graduacion',
    'evaluaciones_tutoria', 'informes_avance', 'reuniones', 'tribunales',
    'estados_conclusion_mg', 'comprobantes_pago_mg', 'notificaciones',
    'documentos_expediente', 'historial_auditoria',
    'bitacora_auditoria_usuarios', 'registro_accesos',
    'configuracion_sistema',
];
foreach ($tablas as $tabla) {
    $pdo->exec("TRUNCATE TABLE `$tabla`");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

// ---------------------------------------------------------------
// Helper de inserción
// ---------------------------------------------------------------
function insertarFilas(PDO $pdo, string $tabla, array $filas): void
{
    if (count($filas) === 0) {
        return;
    }
    $columnas  = array_keys($filas[0]);
    $campos    = implode(',', array_map(fn($c) => "`$c`", $columnas));
    $marcadores = implode(',', array_map(fn($c) => ":$c", $columnas));
    $stmt = $pdo->prepare("INSERT INTO `$tabla` ($campos) VALUES ($marcadores)");
    foreach ($filas as $fila) {
        $parametros = [];
        foreach ($columnas as $c) {
            $parametros[":$c"] = $fila[$c];
        }
        $stmt->execute($parametros);
    }
}

$PASSWORD = password_hash('Control123+', PASSWORD_DEFAULT);
$PASSWORD_ADMIN = password_hash('@g4t.a56', PASSWORD_BCRYPT);
$IP       = '127.0.0.1';

// ---------------------------------------------------------------
// 1. roles (catálogo fijo de autorización)
// ---------------------------------------------------------------
insertarFilas($pdo, 'roles', [
    ['id_rol' => 1, 'nombre_rol' => 'administrador'],
    ['id_rol' => 2, 'nombre_rol' => 'tutor'],
    ['id_rol' => 3, 'nombre_rol' => 'estudiante'],
    ['id_rol' => 4, 'nombre_rol' => 'auxiliar'],
]);

// ---------------------------------------------------------------
// 2. configuracion_sistema (23 parámetros)
// ---------------------------------------------------------------
$config = [
    ['cupo_maximo_mg', '5',  'Limite de estudiantes por tutor en Modalidad de Grado.'],
    ['cupo_minimo_apoyo', '3', 'Numero minimo de estudiantes por tutoria de apoyo para habilitarla.'],
    ['materias_requeridas_mg', '54', 'Materias culminadas requeridas para acceder a Modalidad de Grado.'],
    ['semestres_requeridos_mg', '9', 'Semestres requeridos para acceder a Modalidad de Grado.'],
    ['gestion_academica', '2026', 'Gestion academica vigente.'],
    ['semestre_actual', '1', 'Semestre academico en curso.'],
    ['horario_inicio_consulta', '08:00', 'Hora de inicio de consultas academicas.'],
    ['horario_fin_consulta', '20:00', 'Hora de fin de consultas academicas.'],
    ['tiempo_inactividad_segundos', '1800', 'Segundos de inactividad permitidos antes de cerrar la sesion.'],
    ['max_intentos_login', '5', 'Maximo de intentos de inicio de sesion por usuario.'],
    ['tamanio_maximo_archivo_mb', '5', 'Tamano maximo de archivos adjuntos en megabytes.'],
    ['extensiones_documentos', 'doc,docx,pdf', 'Extensiones permitidas para el expediente de documentos.'],
    ['extensiones_evidencias', 'jpg,jpeg,png,pdf', 'Extensiones permitidas para evidencias de reuniones.'],
    ['minimo_estudiantes_tutoria_grado', '1', 'Estudiantes minimos para iniciar la tutoria de grado.'],
    ['cantidad_tribunales_tutoria', '2', 'Numero estandar de miembros del tribunal evaluador.'],
    ['maximo_tribunales_tutoria', '3', 'Numero maximo de miembros del tribunal evaluador.'],
    ['plazo_dias_carta', '7', 'Plazo en dias para responder la carta de designacion.'],
    ['permite_evaluacion_doble_tutor', '1', 'Habilita la figura de evaluacion con tutor acompanante.'],
    ['url_manual_usuario', '/docs/manual_usuario.pdf', 'Ruta publica del manual de usuario.'],
    ['correo_contacto', 'soporte@upds.edu.bo', 'Correo de contacto del sistema.'],
    ['telefono_contacto', '+59146631234', 'Telefono de contacto de la Direccion Academica.'],
    ['nombre_universidad', 'Universidad Privada Domingo Savio', 'Nombre oficial de la universidad.'],
    ['eslogan', 'Profesionales y humanos', 'Eslogan institucional de la universidad.'],
];
insertarFilas($pdo, 'configuracion_sistema',
    array_map(fn($c) => ['clave' => $c[0], 'valor' => $c[1], 'descripcion' => $c[2]], $config));

// ---------------------------------------------------------------
// 3. carreras (22)
// ---------------------------------------------------------------
$carreras = [
    'Ingenieria de Sistemas', 'Ingenieria Civil', 'Ingenieria Industrial',
    'Arquitectura', 'Administracion de Empresas', 'Contaduria Publica',
    'Auditoria Financiera', 'Derecho', 'Medicina', 'Odontologia',
    'Enfermeria', 'Psicologia', 'Comunicacion Social', 'Economia',
    'Ingenieria Electronica', 'Ingenieria Mecanica', 'Ingenieria Petrolera',
    'Ingenieria Ambiental', 'Licenciatura en Educacion', 'Marketing y Comercio',
    'Ingenieria en Telecomunicaciones', 'Bioquimica y Farmacia',
];
insertarFilas($pdo, 'carreras',
    array_map(fn($n) => ['nombre_carrera' => $n], $carreras));

// ---------------------------------------------------------------
// 4. materias (28) vinculadas a carreras
// ---------------------------------------------------------------
$materias = [
    [1, 'Programacion I'], [1, 'Programacion II'], [1, 'Base de Datos'],
    [1, 'Estructura de Datos'], [1, 'Redes de Computadoras'], [1, 'Seguridad Informatica'],
    [2, 'Calculo I'], [2, 'Calculo II'], [2, 'Fisica I'], [2, 'Resistencia de Materiales'],
    [5, 'Contabilidad General'], [5, 'Costos y Presupuestos'],
    [5, 'Administracion de Operaciones'], [5, 'Gestion de Recursos Humanos'],
    [7, 'Auditoria General'],
    [8, 'Derecho Civil'], [8, 'Derecho Penal'], [8, 'Historia del Derecho'],
    [9, 'Anatomia Humana'],
    [12, 'Psicologia General'], [12, 'Psicologia del Desarrollo'],
    [13, 'Comunicacion Oral y Escrita'],
    [14, 'Microeconomia'], [14, 'Macroeconomia'], [14, 'Estadistica Aplicada'],
    [20, 'Marketing Digital'],
    [22, 'Quimica General'], [22, 'Farmacologia'],
];
insertarFilas($pdo, 'materias',
    array_map(fn($m) => ['nombre_materia' => $m[1], 'id_carrera' => $m[0]], $materias));

// ---------------------------------------------------------------
// 5. modalidades_graduacion (22)
// ---------------------------------------------------------------
$modalidades = [
    ['Proyecto de Grado', 'Elaboracion de un proyecto integral aplicado a la carrera.', 1, 3],
    ['Tesis', 'Investigacion cientifica con defensa publica.', 2, 5],
    ['Trabajo Dirigido', 'Practica profesional supervisada en institucion.', 1, 4],
    ['Graduacion por Excelencia', 'Reconocimiento por rendimiento academico sobresaliente.', 0, 0],
    ['Examen de Grado', 'Examen integral de conocimientos de la carrera.', 0, 0],
    ['Investigacion Aplicada', 'Desarrollo de una investigacion aplicada a la region.', 1, 3],
    ['Proyecto Social Comunitario', 'Proyecto de impacto social en comunidades.', 1, 3],
    ['Plan de Negocios', 'Elaboracion de un plan de negocios viable.', 1, 4],
    ['Emprendimiento Tecnologico', 'Desarrollo de un emprendimiento de base tecnologica.', 1, 3],
    ['Practica Profesional Supervisada', 'Pasantia profesional de grado con informe final.', 1, 4],
    ['Monografia', 'Estudio monografico de un tema especifico.', 1, 3],
    ['Tesina', 'Trabajo de grado de menor extension que una tesis.', 1, 3],
    ['Examen de Suficiencia', 'Evaluacion de suficiencia profesional.', 0, 0],
    ['Produccion Cientifica', 'Publicacion de un articulo en revista indexada.', 1, 2],
    ['Movilidad Academica', 'Culminacion de estudios en universidad extranjera.', 1, 2],
    ['Doble Titulacion', 'Programa de doble titulacion nacional o internacional.', 1, 4],
    ['Curso de Actualizacion Especial', 'Especializacion corta certificada.', 0, 0],
    ['Especialidad Superior', 'Postgrado de especialidad academica.', 1, 4],
    ['Trabajo Final Integrador', 'Trabajo que integra las competencias de la carrera.', 1, 3],
    ['Prototipo Tecnologico', 'Diseno y construccion de un prototipo funcional.', 1, 3],
    ['Estancia Academica', 'Estancia de investigacion en institucion asociada.', 1, 2],
    ['Publicacion de Articulo Cientifico', 'Publicacion de resultados de investigacion.', 1, 2],
];
insertarFilas($pdo, 'modalidades_graduacion',
    array_map(fn($m, $i) => [
        'nombre' => $m[0], 'descripcion' => $m[1],
        'minimo_reuniones_semana' => $m[2], 'cantidad_informes' => $m[3],
        'activa' => in_array($m[0], ['Curso de Actualizacion Especial', 'Especialidad Superior', 'Estancia Academica', 'Graduacion por Excelencia'], true) ? 0 : 1,
    ], $modalidades, array_keys($modalidades)));

// ---------------------------------------------------------------
// 6. estados_conclusion_mg (22)
// ---------------------------------------------------------------
$estados = [
    'Aprobada', 'Reprobada', 'Abandono', 'Otros', 'Defensa Aprobada',
    'Defensa Reprobada', 'Aprobada con Observaciones', 'Trabajo Aprobado',
    'Trabajo Reprobado', 'Sustentacion Aprobada', 'Sustentacion Pendiente',
    'Correcciones Aprobadas', 'Nota Final Registrada', 'Acta Emitida',
    'Diploma en Tramite', 'Diploma Entregado', 'Certificado Emitido',
    'Defensa Diferida', 'Aplazado por Tiempo', 'Reconsideracion Evaluada',
    'Bajo Rendimiento Academico', 'Finalizacion por Convenio',
];
insertarFilas($pdo, 'estados_conclusion_mg',
    array_map(fn($n) => [
        'nombre' => $n,
        'descripcion' => 'Estado registrado por la Direccion Academica al concluir la modalidad de grado.',
    ], $estados));

// ---------------------------------------------------------------
// 7. usuarios (47): 1 administrador, 2 auxiliares, 20 tutores, 24 estudiantes
// ---------------------------------------------------------------
$usuarios = [
    // Administrador
    [1, 'Sistema', 'Administrador', 'admin@sistema-tutorias.edu.bo', 'admin', '+59146631234'],
    // Auxiliares
    [4, 'Coordinacion', 'Academica', 'coordinacion@upds.edu.bo', 'aux1', '+59171400001'],
    [4, 'Laura', 'Vargas', 'laura.vargas@upds.edu.bo', 'aux2', '+59171400002'],
];
$tutorUsuarios = [
    ['tutor1', 'Carlos', 'Zambrana', 'carlos.zambrana@upds.edu.bo', '+59171400003'],
    ['tutor2', 'Maria Fernanda', 'Gutierrez', 'maria.gutierrez@upds.edu.bo', '+59171400004'],
    ['tutor3', 'Luis Antonio', 'Castro', 'luis.castro@upds.edu.bo', '+59171400005'],
    ['tutor4', 'Gabriela', 'Cruz', 'gabriela.cruz@upds.edu.bo', '+59171400006'],
    ['tutor5', 'Jose Ignacio', 'Soliz', 'jose.soliz@upds.edu.bo', '+59171400007'],
    ['tutor6', 'Paola', 'Fernandez', 'paola.fernandez@upds.edu.bo', '+59171400008'],
    ['tutor7', 'Rodrigo', 'Anez', 'rodrigo.anez@upds.edu.bo', '+59171400009'],
    ['tutor8', 'Valentina', 'Suarez', 'valentina.suarez@upds.edu.bo', '+59171400010'],
    ['tutor9', 'Jorge Luis', 'Paredes', 'jorge.paredes@upds.edu.bo', '+59171400011'],
    ['tutor10', 'Andrea', 'Torres', 'andrea.torres@upds.edu.bo', '+59171400012'],
    ['tutor11', 'Miguel Angel', 'Rios', 'miguel.rios@upds.edu.bo', '+59171400013'],
    ['tutor12', 'Camila', 'Villarroel', 'camila.villarroel@upds.edu.bo', '+59171400014'],
    ['tutor13', 'Fernando', 'Orellana', 'fernando.orellana@upds.edu.bo', '+59171400015'],
    ['tutor14', 'Daniela', 'Salinas', 'daniela.salinas@upds.edu.bo', '+59171400016'],
    ['tutor15', 'Pablo', 'Quiroga', 'pablo.quiroga@upds.edu.bo', '+59171400017'],
    ['tutor16', 'Natalia', 'Cabrera', 'natalia.cabrera@upds.edu.bo', '+59171400018'],
    ['tutor17', 'Sebastian', 'Herrera', 'sebastian.herrera@upds.edu.bo', '+59171400019'],
    ['tutor18', 'Romina', 'Fuentes', 'romina.fuentes@upds.edu.bo', '+59171400020'],
    ['tutor19', 'Esteban', 'Molina', 'esteban.molina@upds.edu.bo', '+59171400021'],
    ['tutor20', 'Carla', 'Ortiz', 'carla.ortiz@upds.edu.bo', '+59171400022'],
];
$estNu = [];
foreach (range(1, 24) as $i) {
    $nombres = [
        'Carlos Andres', 'Maria Jose', 'Luis Fernando', 'Camila Alejandra',
        'Diego Alejandro', 'Sofia Belen', 'Mateo Sebastian', 'Valentina Paz',
        'Nicolas Ignacio', 'Jimena Soledad', 'Andres Rodrigo', 'Daniela Fernanda',
        'Sebastian Joel', 'Emilia Luciana', 'Joaquin Tomas', 'Agustina Renata',
        'Benjamin Elias', 'Luciana Martina', 'Thiago Samuel', 'Isabella Lucia',
        'Elian Victor', 'Antonella Sofia', 'Gael Alexander', 'Delfina Nicole',
    ];
    $apellidos = [
        'Toledo', 'Vargas', 'Rojas', 'Flores', 'Paredes', 'Gutierrez',
        'Quispe', 'Morales', 'Herrera', 'Aruquipa', 'Condori', 'Vega',
        'Mamani', 'Orellana', 'Baldivieso', 'Lopez', 'Cardozo', 'Roca',
        'Villca', 'Cabrera', 'Crespo', 'Navia', 'Rocha', 'Vaca',
    ];
    $estNu[] = ["est$i", $nombres[$i - 1], $apellidos[$i - 1], "estudiante$i@upds.edu.bo", '+59171500' . str_pad((string)$i, 3, '0', STR_PAD_LEFT)];
}
foreach ($tutorUsuarios as $tu) {
    $usuarios[] = [2, $tu[1], $tu[2], $tu[3], $tu[0], $tu[4]];
}
foreach ($estNu as $eu) {
    $usuarios[] = [3, $eu[1], $eu[2], $eu[3], $eu[0], $eu[4]];
}
$filasUsuarios = array_map(fn($u) => [
    'id_rol' => $u[0], 'nombre' => $u[1], 'apellido' => $u[2],
    'correo' => $u[3], 'usuario' => $u[4],
    'contrasena_hash' => ($u[4] === 'admin') ? $PASSWORD_ADMIN : $PASSWORD,
    'telefono' => $u[5], 'estado' => 'activo',
], $usuarios);
insertarFilas($pdo, 'usuarios', $filasUsuarios);

// --- mapas de ids reales ---------------------------------------
$idAdmin = 1;                       // id_usuario del administrador
$idTutorUsuario = [0];              // idTutorUsuario[i] = id_usuario del tutor i
foreach (range(1, 20) as $i) {
    $idTutorUsuario[$i] = 3 + $i;   // usuarios 4..23
}
$idEstUser = [0];                   // idEstUser[i] = id_usuario del estudiante i
foreach (range(1, 24) as $i) {
    $idEstUser[$i] = 23 + $i;       // usuarios 24..47
}

// ---------------------------------------------------------------
// 8. tutores (20)
// ---------------------------------------------------------------
$especialidades = [
    'Desarrollo de Software', 'Base de Datos', 'Redes y Telecomunicaciones',
    'Matematicas y Calculo', 'Fisica Aplicada', 'Gestion Empresarial',
    'Contabilidad y Auditoria', 'Derecho Tributario', 'Derecho Civil',
    'Economia', 'Estadistica', 'Marketing', 'Recursos Humanos',
    'Psicologia Educativa', 'Comunicacion', 'Ingenieria Civil',
    'Arquitectura', 'Enfermeria', 'Bioquimica', 'Ingenieria Electronica',
];
insertarFilas($pdo, 'tutores', array_map(function ($i, $esp) use ($idTutorUsuario) {
    return [
        'id_usuario' => $idTutorUsuario[$i],
        'especialidad' => $esp,
        'biografia' => 'Docente investigador con 15 anos de trayectoria profesional y academica en ' . $esp . '.',
    ];
}, range(1, 20), $especialidades));

// ---------------------------------------------------------------
// 9. estudiantes (24) vinculados a carreras
// ---------------------------------------------------------------
$filasEstudiantes = [];
foreach (range(1, 24) as $i) {
    $conMaterias = $i <= 13;                      // candidatos a Modalidad de Grado
    $semestre    = $conMaterias ? (9 + ($i % 2)) : (4 + ($i % 5));
    $completadas = $conMaterias ? (52 + ($i % 9)) : (30 + ($i % 12));
    $aprobado    = $i <= 8;                       // comprobante aprobado: acceso MG habilitado
    $filasEstudiantes[] = [
        'id_usuario' => $idEstUser[$i],
        'id_carrera' => (($i - 1) % 8) + 1,
        'semestre' => $semestre,
        'materias_completadas' => $completadas,
        'acceso_mg_desbloqueado' => $aprobado ? 1 : 0,
        'mg_desbloqueado_por' => $aprobado ? $idAdmin : null,
        'mg_desbloqueado_fecha' => $aprobado ? '2026-08-20 10:00:00' : null,
        'registro_universitario' => 'RU-2026-' . str_pad((string)$i, 3, '0', STR_PAD_LEFT),
    ];
}
insertarFilas($pdo, 'estudiantes', $filasEstudiantes);

// ---------------------------------------------------------------
// 10. tutor_materia (20 tutores x 3 materias = 60)  / 11. docente_carreras (60)
// ---------------------------------------------------------------
$filasTm = [];
$filasDc = [];
foreach (range(1, 20) as $t) {
    foreach ([$t, $t + 8, $t + 16] as $mId) {
        $materiaId = (($mId - 1) % 28) + 1;
        $filasTm[] = ['id_tutor' => $t, 'id_materia' => $materiaId];
    }
    foreach ([$t, $t + 7, $t + 14] as $cId) {
        $carreraId = (($cId - 1) % 22) + 1;
        $filasDc[] = ['id_tutor' => $t, 'id_carrera' => $carreraId];
    }
}
insertarFilas($pdo, 'tutor_materia', $filasTm);
insertarFilas($pdo, 'docente_carreras', $filasDc);

// ---------------------------------------------------------------
// 12. disponibilidad_tutor (20 tutores x 3 bloques = 60)
// ---------------------------------------------------------------
$filasDisp = [];
foreach (range(1, 20) as $t) {
    $filasDisp[] = ['id_tutor' => $t, 'dia_semana' => 'Lunes', 'hora_inicio' => '08:00:00', 'hora_fin' => '10:00:00'];
    $filasDisp[] = ['id_tutor' => $t, 'dia_semana' => 'Miercoles', 'hora_inicio' => '14:00:00', 'hora_fin' => '16:00:00'];
    $filasDisp[] = ['id_tutor' => $t, 'dia_semana' => 'Viernes', 'hora_inicio' => '10:00:00', 'hora_fin' => '12:00:00'];
}
insertarFilas($pdo, 'disponibilidad_tutor', $filasDisp);

// ---------------------------------------------------------------
// 13. tutorias (20 de grado + 8 de apoyo = 28)
// ---------------------------------------------------------------
$estadosGrado = [
    1 => ['finalizada', 1],     2 => ['finalizada', 2],     3 => ['en_proceso', null],
    4 => ['en_proceso', null],  5 => ['aceptada', null],    6 => ['aceptada', null],
    7 => ['asignada', null],    8 => ['asignada', null],    9 => ['cancelada', null],
    10 => ['en_reasignacion', null], 11 => ['realizada', null], 12 => ['realizada', null],
    13 => ['en_proceso', null], 14 => ['aceptada', null],   15 => ['en_proceso', null],
    16 => ['asignada', null],   17 => ['en_proceso', null], 18 => ['finalizada', 3],
    19 => ['realizada', null],  20 => ['en_proceso', null],
];
$filasTutorias = [];
$estadoTutoria = [];   // por id_tutoria
// Grado: estudiantes 1..13 y luego 1..7 para completar 20
$estudiantesGrado = array_merge(range(1, 13), range(1, 7));
foreach (range(1, 20) as $g) {
    $est = $estadosGrado[$g];
    $idTutoria = count($filasTutorias) + 1;
    $estadoTutoria[$idTutoria] = $est[0];
    $filasTutorias[] = [
        'id_estudiante' => $estudiantesGrado[$g - 1],
        'id_tutor'      => (($g - 1) % 20) + 1,
        'id_materia'    => (($g - 1) % 28) + 1,
        'id_modalidad'  => (($g - 1) % 22) + 1,
        'id_estado_conclusion' => $est[1],
        'tipo'  => 'grado',
        'fecha' => '2026-' . str_pad((string)(9 + ($g % 3)), 2, '0', STR_PAD_LEFT) . '-' . str_pad((string)(5 + ($g % 20)), 2, '0', STR_PAD_LEFT),
        'hora_inicio' => '08:30:00',
        'hora_fin'    => '10:30:00',
        'modalidad'   => ($g % 2) ? 'presencial' : 'virtual',
        'lugar_o_enlace' => ($g % 2) ? 'Aula 2 - Bloque B' : 'https://meet.example.com/sesion-' . $g,
        'estado'      => $est[0],
        'observaciones' => ($est[0] === 'cancelada') ? 'Cancelada por solicitud del estudiante.' : 'Tutoria de modalidad de grado.',
        'fecha_solicitud' => '2026-08-' . str_pad((string)(10 + ($g % 15)), 2, '0', STR_PAD_LEFT) . ' 09:00:00',
    ];
}
// Apoyo: estudiantes 21..24 rotando
$estudiantesApoyo = [21, 22, 23, 24, 21, 22, 23, 24];
$estadosApoyo = ['confirmada', 'pendiente', 'realizada', 'confirmada', 'pendiente', 'realizada', 'confirmada', 'pendiente'];
foreach (range(1, 8) as $a) {
    $idTutoria = count($filasTutorias) + 1;
    $estadoTutoria[$idTutoria] = $estadosApoyo[$a - 1];
    $filasTutorias[] = [
        'id_estudiante' => $estudiantesApoyo[$a - 1],
        'id_tutor'      => (($a * 3) % 20) + 1,
        'id_materia'    => (($a * 3) % 28) + 1,
        'id_modalidad'  => null,
        'id_estado_conclusion' => null,
        'tipo'  => 'apoyo',
        'fecha' => '2026-' . str_pad((string)(10 + ($a % 3)), 2, '0', STR_PAD_LEFT) . '-' . str_pad((string)(5 + ($a * 2)), 2, '0', STR_PAD_LEFT),
        'hora_inicio' => '15:00:00',
        'hora_fin'    => '17:00:00',
        'modalidad'   => 'presencial',
        'lugar_o_enlace' => 'Laboratorio 3',
        'estado'      => $estadosApoyo[$a - 1],
        'observaciones' => 'Tutoria de apoyo academico.',
        'fecha_solicitud' => '2026-09-' . str_pad((string)(1 + $a), 2, '0', STR_PAD_LEFT) . ' 14:00:00',
    ];
}
insertarFilas($pdo, 'tutorias', $filasTutorias);

// ---------------------------------------------------------------
// 14. tutoria_estudiantes (8 tutorias de apoyo x 3 = 24)
// ---------------------------------------------------------------
$filasTe = [];
foreach (range(1, 8) as $a) {
    $idTutoria = 20 + $a;
    $k = ($a * 2) % 20;
    $filasTe[] = ['id_tutoria' => $idTutoria, 'id_estudiante' => (($k) % 20) + 1];
    $filasTe[] = ['id_tutoria' => $idTutoria, 'id_estudiante' => (($k + 1) % 20) + 1];
    $filasTe[] = ['id_tutoria' => $idTutoria, 'id_estudiante' => (($k + 2) % 20) + 1];
}
insertarFilas($pdo, 'tutoria_estudiantes', $filasTe);

// ---------------------------------------------------------------
// 15. cartas_designacion (20, una por tutoria de grado)
// ---------------------------------------------------------------
$filasCartas = [];
foreach (range(1, 20) as $g) {
    $estado = $estadoTutoria[$g];
    $cartaEstado = in_array($estado, ['finalizada', 'aceptada', 'en_proceso', 'realizada'], true)
        ? 'aceptada'
        : (($estado === 'cancelada' || $estado === 'en_reasignacion') ? 'rechazada' : 'pendiente');
    $filasCartas[] = [
        'id_tutoria'   => $g,
        'id_tutor'     => (($g - 1) % 20) + 1,
        'id_estudiante'=> $estudiantesGrado[$g - 1],
        'fecha_generacion' => '2026-08-15 10:00:00',
        'fecha_firma'  => ($cartaEstado === 'aceptada') ? '2026-08-18 11:30:00' : null,
        'estado'       => $cartaEstado,
        'motivo_rechazo' => ($cartaEstado === 'rechazada') ? 'El docente no dispone de cupo en esta gestion.' : null,
    ];
}
insertarFilas($pdo, 'cartas_designacion', $filasCartas);

// ---------------------------------------------------------------
// 16. tribunales (20 tutorias de grado x 2 miembros = 40)
// ---------------------------------------------------------------
$filasTribunales = [];
foreach (range(1, 20) as $g) {
    $u1 = 4 + (($g * 3) % 20);
    $u2 = 4 + (($g * 3 + 1) % 20);
    if ($u1 === $u2) { $u2 = $u1 === 23 ? 4 : $u1 + 1; }
    $filasTribunales[] = ['id_usuario' => $u1, 'id_tutoria' => $g];
    $filasTribunales[] = ['id_usuario' => $u2, 'id_tutoria' => $g];
}
insertarFilas($pdo, 'tribunales', $filasTribunales);

// ---------------------------------------------------------------
// 17. informes_avance (24)
// ---------------------------------------------------------------
$filasInformes = [];
$idsConInforme = [
    1 => 2, 2 => 2, 3 => 2, 4 => 2, 5 => 2, 6 => 2,   // 2 informes cada una (12)
    11 => 1, 13 => 1, 15 => 1, 17 => 1,               // 1 cada una        (4)
    18 => 1, 19 => 1, 23 => 1, 26 => 1,               // 1 cada una        (4)
    21 => 1, 24 => 1, 27 => 1, 28 => 1,               // 1 cada una        (4)
];                                                     // total 24
foreach ($idsConInforme as $idTutoria => $cantidad) {
    foreach (range(1, $cantidad) as $n) {
        $filasInformes[] = [
            'id_tutoria' => $idTutoria,
            'numero_informe' => $n,
            'fecha_limite' => '2026-11-15',
            'fecha_registro' => '2026-10-' . str_pad((string)(3 + $n), 2, '0', STR_PAD_LEFT) . ' 09:30:00',
            'porcentaje_avance' => ($n === 1) ? 35 : 70,
            'descripcion_avance' => 'Avance de la tutoria documentado en el informe correspondiente.',
        ];
    }
}
insertarFilas($pdo, 'informes_avance', $filasInformes);

// ---------------------------------------------------------------
// 18. reuniones (24)
// ---------------------------------------------------------------
$filasReuniones = [];
$idsReunion = [1, 2, 3, 4, 5, 6, 11, 12, 13, 14, 15, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 3, 4];
$asistencias = ['si', 'no', 'tardanza'];
foreach ($idsReunion as $n => $idTutoria) {
    $filasReuniones[] = [
        'id_tutoria' => $idTutoria,
        'fecha' => '2026-09-' . str_pad((string)(1 + ($n % 22)), 2, '0', STR_PAD_LEFT),
        'hora_inicio' => '16:00:00',
        'hora_fin'    => '17:30:00',
        'lugar_o_enlace' => 'Aula 5 - Bloque A',
        'asistio_estudiante' => $asistencias[$n % 3],
        'evidencia_url' => ($n % 2) ? 'uploads/evidencias/reunion_prueba_' . ($n + 1) . '.pdf' : null,
        'observaciones' => 'Sesion de seguimiento de la tutoria.',
    ];
}
insertarFilas($pdo, 'reuniones', $filasReuniones);

// ---------------------------------------------------------------
// 19. evaluaciones_tutoria (20)
// ---------------------------------------------------------------
$filasEvaluaciones = [];
foreach (range(1, 20) as $e) {
    $filasEvaluaciones[] = [
        'id_tutoria' => $e,
        'calificacion' => ($e % 5) + 1,
        'comentario' => 'Evaluacion institucional de la tutoria asignada.',
        'fecha_evaluacion' => '2026-11-20 10:00:00',
    ];
}
insertarFilas($pdo, 'evaluaciones_tutoria', $filasEvaluaciones);

// ---------------------------------------------------------------
// 20. comprobantes_pago_mg (20)
// ---------------------------------------------------------------
$filasComprobantes = [];
// Aprobados para estudiantes 1..8
foreach (range(1, 8) as $s) {
    $filasComprobantes[] = [
        'id_estudiante' => $s,
        'monto' => '1500.00',
        'fecha_pago' => '2026-08-10',
        'ruta_archivo' => 'uploads/comprobantes/comprobante_' . $s . '_1.pdf',
        'estado' => 'aprobado',
        'motivo_rechazo' => null,
        'fecha_registro' => '2026-08-10 12:00:00',
        'validado_por' => $idAdmin,
        'fecha_validacion' => '2026-08-12 10:00:00',
    ];
}
// Pendientes estudiantes 9..11
foreach (range(9, 11) as $s) {
    $filasComprobantes[] = [
        'id_estudiante' => $s, 'monto' => '1500.00', 'fecha_pago' => '2026-09-01',
        'ruta_archivo' => 'uploads/comprobantes/comprobante_' . $s . '_1.pdf',
        'estado' => 'pendiente', 'motivo_rechazo' => null,
        'fecha_registro' => '2026-09-01 11:00:00', 'validado_por' => null, 'fecha_validacion' => null,
    ];
}
// Rechazados estudiantes 12..13
foreach (range(12, 13) as $s) {
    $filasComprobantes[] = [
        'id_estudiante' => $s, 'monto' => '1500.00', 'fecha_pago' => '2026-09-03',
        'ruta_archivo' => 'uploads/comprobantes/comprobante_' . $s . '_1.pdf',
        'estado' => 'rechazado', 'motivo_rechazo' => 'El comprobante no es legible. Adjunte una copia clara.',
        'fecha_registro' => '2026-09-03 11:00:00', 'validado_por' => $idAdmin, 'fecha_validacion' => '2026-09-05 09:00:00',
    ];
}
// Segundos comprobantes pendientes estudiantes 1..7 (para llegar a 20 filas)
foreach (range(1, 7) as $s) {
    $filasComprobantes[] = [
        'id_estudiante' => $s, 'monto' => '500.00', 'fecha_pago' => '2026-09-20',
        'ruta_archivo' => 'uploads/comprobantes/comprobante_' . $s . '_2.pdf',
        'estado' => 'pendiente', 'motivo_rechazo' => null,
        'fecha_registro' => '2026-09-20 10:00:00', 'validado_por' => null, 'fecha_validacion' => null,
    ];
}
insertarFilas($pdo, 'comprobantes_pago_mg', $filasComprobantes);

// ---------------------------------------------------------------
// 21. notificaciones (26)
// ---------------------------------------------------------------
$filasNotificaciones = [];
foreach (range(1, 8) as $s) {
    $filasNotificaciones[] = [
        'id_destinatario' => $idEstUser[$s], 'id_origen' => $idAdmin,
        'tipo' => 'TRIBUNAL_ASIGNADO', 'mensaje' => 'Se asigno el tribunal evaluador para su tutoria de grado.',
        'enlace' => '/controllers/tutorias_listar.php', 'leida' => ($s % 2), 'fecha' => '2026-08-25 09:00:00',
    ];
    $filasNotificaciones[] = [
        'id_destinatario' => $idEstUser[$s], 'id_origen' => $idAdmin,
        'tipo' => 'COMPROBANTE_MG_APROBADO', 'mensaje' => 'Su comprobante de pago fue aprobado. Ya puede solicitar la Modalidad de Grado.',
        'enlace' => '/views/estudiante/modalidad_grado.php', 'leida' => 0, 'fecha' => '2026-08-13 10:00:00',
    ];
}
foreach (range(1, 8) as $t) {
    $filasNotificaciones[] = [
        'id_destinatario' => $idTutorUsuario[$t], 'id_origen' => $idAdmin,
        'tipo' => 'TUTORIA_ASIGNADA', 'mensaje' => 'Se le asigno una nueva tutoria. Revise la carta de designacion.',
        'enlace' => '/controllers/cartas_responder.php', 'leida' => 0, 'fecha' => '2026-08-16 09:00:00',
    ];
}
$filasNotificaciones[] = ['id_destinatario' => $idAdmin, 'id_origen' => $idEstUser[9], 'tipo' => 'COMPROBANTE_MG', 'mensaje' => 'El estudiante registro un nuevo comprobante de pago pendiente de validacion.', 'enlace' => '/controllers/mg_comprobantes_listar.php', 'leida' => 0, 'fecha' => '2026-09-01 12:00:00'];
$filasNotificaciones[] = ['id_destinatario' => $idEstUser[14], 'id_origen' => $idAdmin, 'tipo' => 'DOCUMENTO_RECIBIDO', 'mensaje' => 'Se adjunto un documento a su expediente de tutoria.', 'enlace' => '/controllers/expediente_documentos.php', 'leida' => 0, 'fecha' => '2026-09-10 09:00:00'];
$filasNotificaciones[] = ['id_destinatario' => $idTutorUsuario[1], 'id_origen' => $idAdmin, 'tipo' => 'DOCUMENTO_NUEVO', 'mensaje' => 'Su tutoria asignada recibio un nuevo documento en el expediente.', 'enlace' => '/controllers/expediente_documentos.php', 'leida' => 1, 'fecha' => '2026-09-10 09:05:00'];
insertarFilas($pdo, 'notificaciones', $filasNotificaciones);

// ---------------------------------------------------------------
// 22. documentos_expediente (20)
// ---------------------------------------------------------------
$filasDocumentos = [];
foreach (range(1, 20) as $g) {
    $filasDocumentos[] = [
        'id_tutoria' => $g,
        'id_origen' => $idAdmin,
        'id_destinatario' => $idEstUser[$estudiantesGrado[$g - 1]],
        'nombre_original' => 'Acta_tutoria_' . $g . '.pdf',
        'ruta_archivo' => 'uploads/expedientes/acta_' . $g . '.pdf',
        'descripcion' => 'Documento adjunto al expediente de la tutoria.',
        'fecha' => '2026-09-11 10:00:00',
    ];
}
insertarFilas($pdo, 'documentos_expediente', $filasDocumentos);

// ---------------------------------------------------------------
// 23. historial_auditoria (30)
// ---------------------------------------------------------------
$eventosHistorial = [
    ['LOGIN', 'Inicio de sesion exitoso.'],
    ['LOGOUT', 'Cierre de sesion del usuario.'],
    ['TUTORIA_ASIGNADA', 'Asignacion de tutoria de grado con generacion de carta.'],
    ['TRIBUNAL_ASIGNADO', 'Asignacion de miembros del tribunal evaluador.'],
    ['CARTA_ACEPTADA', 'La carta de designacion fue aceptada por el docente.'],
    ['CARTA_RECHAZADA', 'La carta de designacion fue rechazada por el docente.'],
    ['REUNION_REGISTRADA', 'Registro de reunion de seguimiento de la tutoria.'],
    ['INFORME_REGISTRADO', 'Registro de informe de avance de la tutoria.'],
    ['COMPROBANTE_MG', 'El estudiante registro un comprobante de pago.'],
    ['COMPROBANTE_MG_APROBADO', 'El administrador aprobo el comprobante y habilito el acceso a la MG.'],
    ['COMPROBANTE_MG_RECHAZADO', 'El administrador rechazo el comprobante de pago.'],
    ['DOCUMENTO_ENVIADO', 'Se adjunto un documento al expediente de la tutoria.'],
    ['TUTORIA_FINALIZADA', 'La tutoria de modalidad de grado fue finalizada.'],
    ['TUTORIA_CANCELADA', 'La tutoria fue cancelada con registro de motivo.'],
    ['LOGIN', 'Inicio de sesion exitoso del docente.'],
    ['TUTORIA_UNIDO', 'Un estudiante se unio a una tutoria de apoyo.'],
    ['INFORME_REGISTRADO', 'Registro del segundo informe de avance.'],
    ['REUNION_REGISTRADA', 'Registro de reunion con asistencia de tardanza.'],
    ['LOGIN', 'Inicio de sesion exitoso del auxiliar.'],
    ['LOGIN', 'Intento de inicio de sesion fallido.'],
    ['CARTA_ACEPTADA', 'Aceptacion de carta numero 5.'],
    ['TRIBUNAL_MODIFICADO', 'Modificacion de un miembro del tribunal.'],
    ['TRIBUNAL_ELIMINADO', 'Retiro de un miembro del tribunal evaluador.'],
    ['LOGIN', 'Inicio de sesion exitoso.'],
    ['LOGOUT', 'Cierre de sesion del administrador.'],
    ['TUTORIA_ASIGNADA', 'Asignacion de tutoria de apoyo abierta.'],
    ['COMPROBANTE_MG_APROBADO', 'Aprobacion de comprobante del estudiante 5.'],
    ['DOCUMENTO_ENVIADO', 'Adjunto de acta final al expediente.'],
    ['REUNION_REGISTRADA', 'Registro de reunion de cierre de tutoria.'],
    ['TUTORIA_FINALIZADA', 'Finalizacion de la tutoria de grado 1.'],
];
$ipCliente = ['127.0.0.1', '192.168.1.10', '10.0.0.15', '192.168.1.22'];
$filasHistorial = [];
foreach ($eventosHistorial as $n => $ev) {
    $idUsuario = (($n * 7) % 24) + 1;   // usuarios 1..24 (rotando)
    $filasHistorial[] = [
        'id_usuario'  => $idUsuario,
        'tipo_evento' => $ev[0],
        'descripcion' => $ev[1],
        'fecha_hora'  => '2026-09-' . str_pad((string)(1 + ($n % 20)), 2, '0', STR_PAD_LEFT) . ' ' . str_pad((string)(8 + ($n % 11)), 2, '0', STR_PAD_LEFT) . ':15:00',
        'ip_origen'   => $ipCliente[$n % 4],
    ];
}
insertarFilas($pdo, 'historial_auditoria', $filasHistorial);

// ---------------------------------------------------------------
// 24. bitacora_auditoria_usuarios (20)
// ---------------------------------------------------------------
$acciones = ['crear', 'editar', 'eliminar'];
$filasBitacora = [];
foreach (range(1, 20) as $b) {
    $accion = $acciones[$b % 3];
    $afectado = (($b * 5) % 44) + 2;   // usuarios 2..45
    $filasBitacora[] = [
        'id_operador' => ($b % 2) ? $idAdmin : 2,
        'id_afectado' => $afectado,
        'accion' => $accion,
        'detalles' => json_encode(['operacion' => $accion, 'registro' => $afectado, 'usuario' => 'Operacion registrada por el personal autorizado.'], JSON_UNESCAPED_UNICODE),
        'fecha_hora' => '2026-09-' . str_pad((string)(2 + ($b % 18)), 2, '0', STR_PAD_LEFT) . ' 10:' . str_pad((string)($b % 60), 2, '0', STR_PAD_LEFT) . ':00',
        'ip_origen' => $ipCliente[$b % 4],
    ];
}
insertarFilas($pdo, 'bitacora_auditoria_usuarios', $filasBitacora);

// ---------------------------------------------------------------
// 25. registro_accesos (24)
// ---------------------------------------------------------------
$filasAccesos = [];
foreach (range(1, 24) as $c) {
    $resultado = ($c % 5 === 0) ? 'fallido' : 'exitoso';
    $idUsuario = ($c % 5 === 0) ? (($c % 2) ? null : (($c % 24) + 1)) : (($c % 24) + 1);
    $filasAccesos[] = [
        'id_usuario' => $idUsuario,
        'fecha_hora' => '2026-09-' . str_pad((string)(3 + ($c % 18)), 2, '0', STR_PAD_LEFT) . ' ' . str_pad((string)(8 + ($c % 11)), 2, '0', STR_PAD_LEFT) . ':30:00',
        'ip_origen' => $ipCliente[$c % 4],
        'resultado' => $resultado,
    ];
}
insertarFilas($pdo, 'registro_accesos', $filasAccesos);

// ---------------------------------------------------------------
// Reporte final
// ---------------------------------------------------------------
echo "\n=== SEEDER COMPLETADO ===\n";
echo "Contrasena del administrador (admin): @g4t.a56\n";
echo "Contrasena base del resto de usuarios: Control123+\n";
echo "(hashes generados con password_hash() en PHP)\n\n";
$tablasReporte = [
    'roles', 'configuracion_sistema', 'carreras', 'materias',
    'modalidades_graduacion', 'estados_conclusion_mg', 'usuarios',
    'tutores', 'estudiantes', 'tutor_materia', 'docente_carreras',
    'disponibilidad_tutor', 'tutorias', 'tutoria_estudiantes',
    'cartas_designacion', 'tribunales', 'informes_avance', 'reuniones',
    'evaluaciones_tutoria', 'comprobantes_pago_mg', 'notificaciones',
    'documentos_expediente', 'historial_auditoria',
    'bitacora_auditoria_usuarios', 'registro_accesos',
];
foreach ($tablasReporte as $tabla) {
    $n = (int) $pdo->query("SELECT COUNT(*) FROM `$tabla`")->fetchColumn();
    printf("  %-28s %4d filas\n", $tabla, $n);
}
echo "\nUsuarios de prueba para iniciar sesion:\n";
echo "  admin (Administrador)  -> @g4t.a56\n";
echo "  aux1 (Auxiliar)        -> Control123+\n";
echo "  tutor1 (Tutor)         -> Control123+\n";
echo "  est1 (Estudiante)      -> Control123+\n";
echo "  Se puede iniciar sesion con el nombre de usuario o el correo.\n\n";