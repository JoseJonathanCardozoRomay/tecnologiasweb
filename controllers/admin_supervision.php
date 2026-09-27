<?php
require_once __DIR__ . '/../includes/verificar_sesion.php';
require_once __DIR__ . '/../includes/auth.php';
requerirRol(['administrador', 'auxiliar']);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/InformeModel.php';
require_once __DIR__ . '/../models/ReunionModel.php';
require_once __DIR__ . '/../models/TutoriaModel.php';

$informeModel = new InformeModel($pdo);
$reunionModel = new ReunionModel($pdo);
$tutoriaModel = new TutoriaModel($pdo);

/* ---------- INFORMES DE AVANCE (tutores) ---------- */
$informes = [];
$stmt = $pdo->query("
    SELECT i.*, t.id_tutoria, t.estado AS estado_tutoria, t.tipo,
           m.nombre_materia,
           ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido,
           es.nombre AS estudiante_nombre, es.apellido AS estudiante_apellido
    FROM informes_avance i
    INNER JOIN tutorias t ON i.id_tutoria = t.id_tutoria
    INNER JOIN tutores tu ON t.id_tutor = tu.id_tutor
    INNER JOIN usuarios ut ON ut.id_usuario = tu.id_usuario
    INNER JOIN materias m ON t.id_materia = m.id_materia
    INNER JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
    INNER JOIN estudiantes e ON e.id_estudiante = te.id_estudiante
    INNER JOIN usuarios es ON es.id_usuario = e.id_usuario
    ORDER BY i.fecha_registro DESC
");
$informes = $stmt->fetchAll();

/* ---------- EVIDENCIAS DE REUNIONES (estudiantes) ---------- */
$evidencias = [];
$stmt = $pdo->query("
    SELECT r.*, t.id_tutoria, t.estado AS estado_tutoria, t.tipo,
           m.nombre_materia,
           ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido,
           es.nombre AS estudiante_nombre, es.apellido AS estudiante_apellido
    FROM reuniones r
    INNER JOIN tutorias t ON r.id_tutoria = t.id_tutoria
    INNER JOIN tutores tu ON t.id_tutor = tu.id_tutor
    INNER JOIN usuarios ut ON ut.id_usuario = tu.id_usuario
    INNER JOIN materias m ON t.id_materia = m.id_materia
    INNER JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
    INNER JOIN estudiantes e ON e.id_estudiante = te.id_estudiante
    INNER JOIN usuarios es ON es.id_usuario = e.id_usuario
    WHERE r.evidencia_url IS NOT NULL AND r.evidencia_url != ''
    ORDER BY r.fecha DESC, r.hora_inicio DESC
");
$evidencias = $stmt->fetchAll();

/* ---------- DOCUMENTOS DE EXPEDIENTE ---------- */
$documentosExpediente = [];
$stmt = $pdo->query("
    SELECT d.*, t.id_tutoria, t.estado AS estado_tutoria, t.tipo,
           m.nombre_materia,
           ut.nombre AS tutor_nombre, ut.apellido AS tutor_apellido,
           es.nombre AS estudiante_nombre, es.apellido AS estudiante_apellido,
           ori.nombre AS origen_nombre, ori.apellido AS origen_apellido,
           dest.nombre AS dest_nombre, dest.apellido AS dest_apellido
    FROM documentos_expediente d
    INNER JOIN tutorias t ON d.id_tutoria = t.id_tutoria
    INNER JOIN materias m ON t.id_materia = m.id_materia
    INNER JOIN tutores tu ON t.id_tutor = tu.id_tutor
    INNER JOIN usuarios ut ON ut.id_usuario = tu.id_usuario
    INNER JOIN tutoria_estudiantes te ON te.id_tutoria = t.id_tutoria
    INNER JOIN estudiantes e ON e.id_estudiante = te.id_estudiante
    INNER JOIN usuarios es ON es.id_usuario = e.id_usuario
    INNER JOIN usuarios ori ON ori.id_usuario = d.id_origen
    INNER JOIN usuarios dest ON dest.id_usuario = d.id_destinatario
    ORDER BY d.fecha DESC
");
$documentosExpediente = $stmt->fetchAll();

$tituloPagina = 'Supervision de Informes y Evidencias - UPDS';
include __DIR__ . '/../views/admin/supervision_informes_evidencias.php';