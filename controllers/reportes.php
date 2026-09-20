<?php
require_once __DIR__.'/../includes/verificar_sesion.php';require_once __DIR__.'/../includes/funciones.php';requireRole(['administrador']);require_once __DIR__.'/../config/conexion.php';
$resumen=[
 'total'=>(int)$pdo->query('SELECT COUNT(*) FROM tutorias')->fetchColumn(),
 'pendientes'=>(int)$pdo->query("SELECT COUNT(*) FROM tutorias WHERE estado='pendiente'")->fetchColumn(),
 'confirmadas'=>(int)$pdo->query("SELECT COUNT(*) FROM tutorias WHERE estado='confirmada'")->fetchColumn(),
 'realizadas'=>(int)$pdo->query("SELECT COUNT(*) FROM tutorias WHERE estado='realizada'")->fetchColumn(),
 'canceladas'=>(int)$pdo->query("SELECT COUNT(*) FROM tutorias WHERE estado='cancelada'")->fetchColumn(),
];
$tutores=$pdo->query("SELECT CONCAT(u.nombre,' ',u.apellido) tutor,COUNT(tu.id_tutoria) tutorias,SUM(tu.estado='realizada') realizadas,COALESCE(ROUND(AVG(ev.calificacion),2),0) promedio FROM tutores t INNER JOIN usuarios u ON u.id_usuario=t.id_usuario LEFT JOIN tutorias tu ON tu.id_tutor=t.id_tutor LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=tu.id_tutoria GROUP BY t.id_tutor,u.nombre,u.apellido ORDER BY tutorias DESC")->fetchAll();
$materias=$pdo->query("SELECT m.nombre_materia,COUNT(tu.id_tutoria) tutorias,COALESCE(ROUND(AVG(ev.calificacion),2),0) promedio FROM materias m LEFT JOIN tutorias tu ON tu.id_materia=m.id_materia LEFT JOIN evaluaciones_tutoria ev ON ev.id_tutoria=tu.id_tutoria GROUP BY m.id_materia,m.nombre_materia ORDER BY tutorias DESC,m.nombre_materia")->fetchAll();
$tituloPagina='Reportes - Sistema de Tutorías';require __DIR__.'/../views/reportes.php';
