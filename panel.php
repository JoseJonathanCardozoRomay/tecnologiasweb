<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$nombre_usuario = $_SESSION['usuario_nombre'] ?? 'Administrador General';
$rol_usuario = $_SESSION['usuario_rol'] ?? 'Administrador';

// Conexión robusta a MySQL
$pdo = null;
$mensaje = '';
$tipo_alerta = 'success';
$dbname = 'sistema_tutorias';

$possibleHosts = ['db', 'tecnologiasweb-db-1', '127.0.0.1', 'localhost'];

foreach ($possibleHosts as $host) {
    try {
        $pdo = new PDO("mysql:host=$host;port=3306;charset=utf8mb4", 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3
        ]);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `$dbname`;");
        break;
    } catch (Throwable $e) {
        $pdo = null;
    }
}

// Arrays globales para generación de datos aleatorios
$nombres = ['Alejandro', 'Ana', 'Carlos', 'Carla', 'Daniel', 'Diana', 'Eduardo', 'Elena', 'Fernando', 'Florencia', 'Gabriel', 'Gabriela', 'Héctor', 'Isabel', 'Javier', 'Jimena', 'Kevin', 'Karla', 'Lucas', 'Lucía', 'Mario', 'María', 'Nicolás', 'Natalia', 'Oscar', 'Olivia', 'Pablo', 'Paula', 'Rodrigo', 'Romina', 'Santiago', 'Sofía', 'Tomás', 'Valeria', 'Vicente', 'Victoria', 'Walter', 'Ximena', 'Yamil', 'Zoe'];
$apellidos = ['Vargas', 'Rios', 'Pérez', 'Gómez', 'Quispe', 'Mendoza', 'Solis', 'Mamani', 'Flores', 'Gonzales', 'Rodríguez', 'López', 'Martínez', 'Fernández', 'García', 'Torres', 'Ramírez', 'Cruz', 'Morales', 'Ortiz', 'Gutiérrez', 'Chávez', 'Ramos', 'Castillo', 'Vaca', 'Rivera', 'Rojas', 'Silva', 'Medina', 'Sánchez'];

if (!$pdo) {
    $mensaje = "Error crítico: No se pudo establecer conexión con MySQL.";
    $tipo_alerta = 'danger';
} else {
    try {
        // Inicialización de esquemas base relacionales
        $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            usuario VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol VARCHAR(50) DEFAULT 'Estudiante',
            estado VARCHAR(20) DEFAULT 'Activo',
            fecha_registro DATE DEFAULT (CURRENT_DATE)
        ) ENGINE=InnoDB;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS carreras (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            facultad VARCHAR(150) NOT NULL
        ) ENGINE=InnoDB;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS materias (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            codigo VARCHAR(50) NOT NULL UNIQUE,
            semestre INT NOT NULL,
            carrera VARCHAR(150) NOT NULL
        ) ENGINE=InnoDB;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS tutores (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(150) NOT NULL,
            especialidad VARCHAR(150) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            telefono VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS tutorias (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            estudiante VARCHAR(150) NOT NULL, 
            tutor VARCHAR(150) NOT NULL, 
            materia VARCHAR(150) NOT NULL, 
            fecha DATETIME NOT NULL, 
            estado VARCHAR(50) NOT NULL
        ) ENGINE=InnoDB;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS tribunales (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            estudiante VARCHAR(150) NOT NULL, 
            proyecto VARCHAR(200) NOT NULL, 
            tribunal_1 VARCHAR(150) NOT NULL, 
            tribunal_2 VARCHAR(150) NOT NULL, 
            fecha DATE NOT NULL
        ) ENGINE=InnoDB;");

        // Seed profesional transaccional: 200 Usuarios
        $totalUsuarios = (int)$pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
        if ($totalUsuarios < 10) {
            $pdo->beginTransaction();
            $stmtUser = $pdo->prepare("INSERT IGNORE INTO usuarios (nombre, email, usuario, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
            
            // Asegurar usuarios base
            $stmtUser->execute(['Ana Rios', 'ana.rios@upds.edu.bo', 'ana', '12345', 'Estudiante', 'Activo']);
            $stmtUser->execute(['Rodrigo Vargas', 'rodrigo.vargas@upds.edu.bo', 'rodrigo', '12345', 'Tutor', 'Activo']);
            $stmtUser->execute(['Osmar Tapia', 'osmar@gmail.com', 'osmar', '12345', 'Estudiante', 'Activo']);

            $dominios = ['upds.edu.bo', 'gmail.com', 'outlook.com', 'estudiantes.upds.edu.bo'];
            $roles = ['Estudiante', 'Estudiante', 'Estudiante', 'Estudiante', 'Tutor', 'Tutor', 'Administrador'];

            for ($i = 1; $i <= 200; $i++) {
                $nombre = $nombres[array_rand($nombres)];
                $apellido1 = $apellidos[array_rand($apellidos)];
                $apellido2 = $apellidos[array_rand($apellidos)];
                $nombreCompleto = "$nombre $apellido1 $apellido2";
                $usuarioLogin = strtolower($nombre . '.' . $apellido1 . '_' . $i);
                $email = strtolower($nombre . '.' . $apellido1 . $i . '@' . $dominios[array_rand($dominios)]);
                $rol = $roles[array_rand($roles)];
                $estado = (rand(1, 15) === 15) ? 'Inactivo' : 'Activo';

                $stmtUser->execute([$nombreCompleto, $email, $usuarioLogin, '12345', $rol, $estado]);
            }
            $pdo->commit();
        }

        // Seed profesional transaccional: 200 Carreras y Materias
        $totalCarreras = (int)$pdo->query("SELECT COUNT(*) FROM carreras")->fetchColumn();
        if ($totalCarreras < 10) {
            $pdo->beginTransaction();
            $stmtCarrera = $pdo->prepare("INSERT IGNORE INTO carreras (nombre, codigo, facultad) VALUES (?, ?, ?)");
            $stmtMateria = $pdo->prepare("INSERT IGNORE INTO materias (nombre, codigo, semestre, carrera) VALUES (?, ?, ?, ?)");

            $facultades = [
                'Facultad de Ingeniería y Tecnología',
                'Facultad de Ciencias Empresariales',
                'Facultad de Ciencias Jurídicas y Sociales',
                'Facultad de Ciencias de la Salud'
            ];

            $areasBase = [
                'Ingeniería de Sistemas', 'Ingeniería Comercial', 'Derecho', 'Administración de Empresas',
                'Contaduría Pública', 'Ingeniería Financiera', 'Psicología', 'Ingeniería Civil',
                'Ingeniería Electromecánica', 'Marketing y Publicidad', 'Arquitectura', 'Diseño Gráfico',
                'Comunicación Social', 'Enfermería', 'Fisioterapia', 'Bioquímica y Farmacia', 'Odontología',
                'Nutrición y Dietética', 'Economía', 'Relaciones Internacionales'
            ];

            $contadorCarreras = 0;
            foreach ($areasBase as $area) {
                for ($i = 1; $i <= 10; $i++) {
                    $contadorCarreras++;
                    $nombreCarrera = "$area - Mención $i";
                    $codigoCarrera = strtoupper(substr(str_replace(' ', '', $area), 0, 4)) . "-30" . $contadorCarreras;
                    $facultad = $facultades[($contadorCarreras - 1) % count($facultades)];

                    $stmtCarrera->execute([$nombreCarrera, $codigoCarrera, $facultad]);

                    $nombresMaterias = ['Introducción a ' . $area, 'Metodología Profesional', 'Taller Especializado I', 'Seminario de Aplicación'];
                    for ($sem = 1; $sem <= 4; $sem++) {
                        $matNombre = $nombresMaterias[$sem - 1] . " (" . $contadorCarreras . ")";
                        $matCodigo = "MAT-" . $contadorCarreras . "-" . $sem;
                        $stmtMateria->execute([$matNombre, $matCodigo, $sem, $nombreCarrera]);
                    }
                }
            }
            $pdo->commit();
        }

        // Seed profesional transaccional: 50 Tutores Académicos
        $totalTutores = (int)$pdo->query("SELECT COUNT(*) FROM tutores")->fetchColumn();
        if ($totalTutores < 10) {
            $pdo->beginTransaction();
            $stmtTutor = $pdo->prepare("INSERT IGNORE INTO tutores (nombre, especialidad, email, telefono) VALUES (?, ?, ?, ?)");
            
            $especialidades = [
                'Ingeniería de Software y Web', 'Bases de Datos Avanzadas', 'Redes y Ciberseguridad', 
                'Matemáticas Aplicadas', 'Contabilidad y Finanzas', 'Derecho Corporativo y Civil', 
                'Marketing Estratégico', 'Gestión de Proyectos Ágiles', 'Inteligencia Artificial', 
                'Estadística y Probabilidad', 'Arquitectura de Computadoras', 'Metodología de Investigación'
            ];

            for ($i = 1; $i <= 50; $i++) {
                $nombreTutor = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];
                $especialidad = $especialidades[array_rand($especialidades)];
                $emailTutor = strtolower(str_replace(' ', '.', $nombreTutor) . $i . '@upds.edu.bo');
                $telefonoTutor = '+591 7' . rand(10, 79) . sprintf('%05d', rand(0, 99999));

                $stmtTutor->execute([$nombreTutor, $especialidad, $emailTutor, $telefonoTutor]);
            }
            $pdo->commit();
        }

        // Seed profesional transaccional: 50 Tribunales de Grado
        $totalTribunales = (int)$pdo->query("SELECT COUNT(*) FROM tribunales")->fetchColumn();
        if ($totalTribunales < 10) {
            $pdo->beginTransaction();
            $stmtTribunal = $pdo->prepare("INSERT IGNORE INTO tribunales (estudiante, proyecto, tribunal_1, tribunal_2, fecha) VALUES (?, ?, ?, ?, ?)");
            
            $temasProyectos = [
                'Sistema Web de Gestión Académica con Angular y PHP',
                'Optimización Logística en Viveros y Viñedos mediante IoT',
                'Plataforma de Comercio Electrónico para PYMES en Bolivia',
                'Aplicación Móvil de Geolocalización para Seguridad Ciudadana',
                'Auditoría de Seguridad Informática en Redes Corporativas',
                'Modelo Predictivo de Rendimiento Estudiantil con Machine Learning',
                'Automatización de Procesos Administrativos con Microservicios',
                'Sistema de Control de Inventarios y Facturación en la Nube',
                'Plataforma de Telemedicina y Gestión de Historias Clínicas',
                'Desarrollo de Videojuego Educativo para Enseñanza de Matemáticas'
            ];

            for ($i = 1; $i <= 50; $i++) {
                $estudianteTribunal = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];
                $proyecto = $temasProyectos[array_rand($temasProyectos)] . ' (Edición ' . $i . ')';
                $trib1 = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
                $trib2 = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
                $fechaDefensa = date('Y-m-d', strtotime('+' . rand(1, 60) . ' days'));

                $stmtTribunal->execute([$estudianteTribunal, $proyecto, $trib1, $trib2, $fechaDefensa]);
            }
            $pdo->commit();
        }

        // Seed profesional transaccional: 50 Tutorías Programadas
        $totalTutorias = (int)$pdo->query("SELECT COUNT(*) FROM tutorias")->fetchColumn();
        if ($totalTutorias < 10) {
            $pdo->beginTransaction();
            $stmtTutoria = $pdo->prepare("INSERT IGNORE INTO tutorias (estudiante, tutor, materia, fecha, estado) VALUES (?, ?, ?, ?, ?)");
            
            $estudiantesList = $pdo->query("SELECT nombre FROM usuarios WHERE rol='Estudiante' LIMIT 40")->fetchAll(PDO::FETCH_COLUMN);
            $tutoresList = $pdo->query("SELECT nombre FROM tutores LIMIT 40")->fetchAll(PDO::FETCH_COLUMN);
            $materiasList = $pdo->query("SELECT nombre FROM materias LIMIT 40")->fetchAll(PDO::FETCH_COLUMN);

            if (empty($estudiantesList)) $estudiantesList = ['Ana Rios', 'Osmar Tapia'];
            if (empty($tutoresList)) $tutoresList = ['Rodrigo Vargas', 'Juan Perez'];
            if (empty($materiasList)) $materiasList = ['Programación IV', 'Bases de Datos II'];

            $estadosTutoria = ['Programada', 'Completada', 'Pendiente de Confirmación'];

            for ($i = 1; $i <= 50; $i++) {
                $estudiante = $estudiantesList[array_rand($estudiantesList)];
                $tutor = $tutoresList[array_rand($tutoresList)];
                $materia = $materiasList[array_rand($materiasList)];
                $fechaTutoria = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days ' . rand(8, 19) . ':00:00'));
                $estadoTutoria = $estadosTutoria[array_rand($estadosTutoria)];

                $stmtTutoria->execute([$estudiante, $tutor, $materia, $fechaTutoria, $estadoTutoria]);
            }
            $pdo->commit();
        }

    } catch (Throwable $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $mensaje = "Aviso de inicialización: " . $e->getMessage();
        $tipo_alerta = 'warning';
    }
}

// PROCESAR ACCIONES POST (CRUD + EDITAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$pdo) {
        $mensaje = "Base de datos no conectada.";
        $tipo_alerta = 'danger';
    } else {
        $accion = $_POST['accion'] ?? '';
        try {
            if ($accion === 'crear_usuario') {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, usuario, password, rol, estado) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['nombre'], 
                    $_POST['email'], 
                    $_POST['usuario'], 
                    !empty($_POST['password']) ? $_POST['password'] : '12345', 
                    $_POST['rol'],
                    $_POST['estado'] ?? 'Activo'
                ]);
                $mensaje = "¡Usuario guardado correctamente!";
            } elseif ($accion === 'editar_usuario') {
                if (!empty($_POST['password'])) {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, usuario=?, password=?, rol=?, estado=? WHERE id=?");
                    $stmt->execute([$_POST['nombre'], $_POST['email'], $_POST['usuario'], $_POST['password'], $_POST['rol'], $_POST['estado'], $_POST['id']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios SET nombre=?, email=?, usuario=?, rol=?, estado=? WHERE id=?");
                    $stmt->execute([$_POST['nombre'], $_POST['email'], $_POST['usuario'], $_POST['rol'], $_POST['estado'], $_POST['id']]);
                }
                $mensaje = "¡Usuario actualizado correctamente!";
            } elseif ($accion === 'eliminar_usuario') {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Usuario eliminado.";
            } elseif ($accion === 'crear_carrera') {
                $stmt = $pdo->prepare("INSERT INTO carreras (nombre, codigo, facultad) VALUES (?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['codigo'], $_POST['facultad']]);
                $mensaje = "¡Carrera registrada con éxito!";
            } elseif ($accion === 'eliminar_carrera') {
                $stmt = $pdo->prepare("DELETE FROM carreras WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Carrera eliminada.";
            } elseif ($accion === 'crear_materia') {
                $stmt = $pdo->prepare("INSERT INTO materias (nombre, codigo, semestre, carrera) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['codigo'], $_POST['semestre'], $_POST['carrera']]);
                $mensaje = "¡Materia registrada con éxito!";
            } elseif ($accion === 'eliminar_materia') {
                $stmt = $pdo->prepare("DELETE FROM materias WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Materia eliminada.";
            } elseif ($accion === 'crear_tutor') {
                $stmt = $pdo->prepare("INSERT INTO tutores (nombre, especialidad, email, telefono) VALUES (?, ?, ?, ?)");
                $stmt->execute([$_POST['nombre'], $_POST['especialidad'], $_POST['email'], $_POST['telefono']]);
                $mensaje = "¡Tutor registrado con éxito!";
            } elseif ($accion === 'eliminar_tutor') {
                $stmt = $pdo->prepare("DELETE FROM tutores WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Tutor eliminado.";
            } elseif ($accion === 'asignar_tutoria') {
                $stmt = $pdo->prepare("INSERT INTO tutorias (estudiante, tutor, materia, fecha, estado) VALUES (?, ?, ?, ?, 'Programada')");
                $stmt->execute([$_POST['estudiante'], $_POST['tutor'], $_POST['materia'], $_POST['fecha']]);
                $mensaje = "¡Tutoría asignada correctamente!";
            } elseif ($accion === 'eliminar_tutoria') {
                $stmt = $pdo->prepare("DELETE FROM tutorias WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Tutoría eliminada.";
            } elseif ($accion === 'crear_tribunal') {
                $stmt = $pdo->prepare("INSERT INTO tribunales (estudiante, proyecto, tribunal_1, tribunal_2, fecha) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_POST['estudiante'], $_POST['proyecto'], $_POST['tribunal_1'], $_POST['tribunal_2'], $_POST['fecha']]);
                $mensaje = "¡Tribunal registrado correctamente!";
            } elseif ($accion === 'eliminar_tribunal') {
                $stmt = $pdo->prepare("DELETE FROM tribunales WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                $mensaje = "Tribunal eliminado.";
            }
            $tipo_alerta = 'success';
        } catch (Throwable $e) {
            $tipo_alerta = 'danger';
            $mensaje = "Error SQL: " . $e->getMessage();
        }
    }
}

$seccion = $_GET['seccion'] ?? 'usuarios';
$datos = [];
$estudiantes = [];
$tutores = [];
$materias = [];

if ($pdo) {
    try {
        $pdo->exec("USE `$dbname`;");
        if ($seccion === 'usuarios') $datos = $pdo->query("SELECT * FROM usuarios ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        elseif ($seccion === 'carreras') $datos = $pdo->query("SELECT * FROM carreras ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        elseif ($seccion === 'materias') $datos = $pdo->query("SELECT * FROM materias ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        elseif ($seccion === 'tutores') $datos = $pdo->query("SELECT * FROM tutores ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        elseif ($seccion === 'tutorias') $datos = $pdo->query("SELECT * FROM tutorias ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        elseif ($seccion === 'asignar') {
            $estudiantes = $pdo->query("SELECT * FROM usuarios WHERE rol='Estudiante'")->fetchAll(PDO::FETCH_ASSOC);
            $tutores = $pdo->query("SELECT * FROM tutores")->fetchAll(PDO::FETCH_ASSOC);
            $materias = $pdo->query("SELECT * FROM materias")->fetchAll(PDO::FETCH_ASSOC);
        }
        elseif ($seccion === 'tribunales') $datos = $pdo->query("SELECT * FROM tribunales ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {}
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración - UPDS Tutorías</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { width: 260px; height: 100vh; background-color: #071930; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; color: #fff; z-index: 1000; }
        .sidebar-brand { padding: 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-brand h6 { font-size: 0.85rem; font-weight: bold; margin: 0; color: #fff; }
        .sidebar-brand p { font-size: 0.65rem; color: #adb5bd; margin: 0; text-transform: uppercase; }
        .sidebar-menu { padding: 1rem 0; overflow-y: auto; flex-grow: 1; }
        .menu-category { font-size: 0.7rem; text-transform: uppercase; color: #6c757d; padding: 0.5rem 1.25rem; font-weight: bold; }
        .sidebar-link { display: flex; align-items: center; padding: 0.65rem 1.25rem; color: #adb5bd; text-decoration: none; transition: all 0.2s; font-size: 0.9rem; }
        .sidebar-link:hover, .sidebar-link.active { color: #fff; background-color: rgba(255,255,255,0.1); border-left: 4px solid #0d6efd; }
        .sidebar-link i { margin-right: 0.75rem; font-size: 1.1rem; }
        .main-content { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .top-navbar { background: #fff; height: 70px; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; }
        .content-body { padding: 2rem; }
        .card-table { background: #fff; border: none; border-radius: 8px; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h6>Sistema de Tutorías</h6>
            <p>Universidad Privada Domingo Savio</p>
        </div>
        <div class="sidebar-menu">
            <div class="menu-category">Gestión Académica</div>
            <a href="panel.php?seccion=usuarios" class="sidebar-link <?= $seccion === 'usuarios' ? 'active' : '' ?>"><i class="bi bi-people"></i> Usuarios</a>
            <a href="panel.php?seccion=carreras" class="sidebar-link <?= $seccion === 'carreras' ? 'active' : '' ?>"><i class="bi bi-journal-bookmark"></i> Carreras</a>
            <a href="panel.php?seccion=materias" class="sidebar-link <?= $seccion === 'materias' ? 'active' : '' ?>"><i class="bi bi-book"></i> Materias</a>
            <a href="panel.php?seccion=tutores" class="sidebar-link <?= $seccion === 'tutores' ? 'active' : '' ?>"><i class="bi bi-person-badge"></i> Tutores</a>

            <div class="menu-category mt-3">Tutorías</div>
            <a href="panel.php?seccion=tutorias" class="sidebar-link <?= $seccion === 'tutorias' ? 'active' : '' ?>"><i class="bi bi-calendar-check"></i> Tutorías</a>
            <a href="panel.php?seccion=asignar" class="sidebar-link <?= $seccion === 'asignar' ? 'active' : '' ?>"><i class="bi bi-person-plus"></i> Asignar tutoría</a>
            <a href="panel.php?seccion=tribunales" class="sidebar-link <?= $seccion === 'tribunales' ? 'active' : '' ?>"><i class="bi bi-shield-check"></i> Tribunales</a>
        </div>
        <div class="p-3 border-top border-secondary d-flex align-items-center justify-content-between bg-dark">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 35px; height: 35px; font-size: 0.85rem;">AS</div>
                <div>
                    <div class="fw-bold small text-white"><?= htmlspecialchars($nombre_usuario) ?></div>
                    <div class="text-white-50" style="font-size: 0.7rem;"><?= htmlspecialchars($rol_usuario) ?></div>
                </div>
            </div>
            <a href="panel.php?logout=1" class="text-white-50 text-decoration-none" title="Salir"><i class="bi bi-box-arrow-right fs-5"></i></a>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="top-navbar">
            <span class="fw-bold text-secondary text-uppercase small" style="letter-spacing: 0.5px;"><?= ucfirst($seccion) ?> - Sistema de Tutorías</span>
            <div class="d-flex align-items-center">
                <a href="panel.php?logout=1" class="btn btn-outline-secondary btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Salir</a>
            </div>
        </div>

        <div class="content-body">
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-<?= $tipo_alerta ?> alert-dismissible fade show shadow-sm" role="alert">
                    <?= htmlspecialchars($mensaje) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- SECCIÓN: USUARIOS -->
            <?php if ($seccion === 'usuarios'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Usuarios del Sistema <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Directorio completo de cuentas institucionales (Contraseña universal: 12345).</p>
                    </div>
                    <button class="btn btn-dark btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalUsuario"><i class="bi bi-person-plus-fill me-1"></i> Nuevo Usuario</button>
                </div>

                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Nombre</th>
                                    <th class="py-3">Correo Electrónico</th>
                                    <th class="py-3">Rol</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($datos)): ?>
                                    <tr><td colspan="6" class="text-center py-4 text-muted">No hay usuarios registrados.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($datos as $u): ?>
                                    <tr>
                                        <td class="ps-3 fw-semibold">#<?= $u['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($u['nombre']) ?></strong><br><small class="text-muted">@<?= htmlspecialchars($u['usuario']) ?></small></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <?php 
                                                $rolBadge = 'bg-primary text-primary';
                                                if ($u['rol'] === 'Tutor') $rolBadge = 'bg-info text-dark';
                                                if ($u['rol'] === 'Administrador') $rolBadge = 'bg-dark text-white';
                                            ?>
                                            <span class="badge <?= $rolBadge ?> bg-opacity-10 border"><?= htmlspecialchars($u['rol']) ?></span>
                                        </td>
                                        <td><span class="badge bg-success bg-opacity-10 text-success border"><?= htmlspecialchars($u['estado']) ?></span></td>
                                        <td class="text-end pe-3">
                                            <button class="btn btn-light btn-sm text-primary border me-1 btn-editar" 
                                                data-id="<?= $u['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($u['nombre'], ENT_QUOTES) ?>"
                                                data-email="<?= htmlspecialchars($u['email'], ENT_QUOTES) ?>"
                                                data-usuario="<?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?>"
                                                data-rol="<?= htmlspecialchars($u['rol'], ENT_QUOTES) ?>"
                                                data-estado="<?= htmlspecialchars($u['estado'], ENT_QUOTES) ?>"
                                                data-bs-toggle="modal" data-bs-target="#modalEditarUsuario" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                                <input type="hidden" name="accion" value="eliminar_usuario">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="btn btn-light btn-sm text-danger border" title="Eliminar"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECCIÓN: CARRERAS -->
            <?php elseif ($seccion === 'carreras'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Carreras Académicas <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Gestión de carreras oficiales de la UPDS (200 carreras registradas).</p>
                    </div>
                    <button class="btn btn-dark btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalCarrera"><i class="bi bi-plus-lg me-1"></i> Nueva Carrera</button>
                </div>
                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Código</th>
                                    <th class="py-3">Nombre de Carrera</th>
                                    <th class="py-3">Facultad</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos as $c): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $c['id'] ?></td>
                                    <td><code><?= htmlspecialchars($c['codigo']) ?></code></td>
                                    <td><strong><?= htmlspecialchars($c['nombre']) ?></strong></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary border"><?= htmlspecialchars($c['facultad']) ?></span></td>
                                    <td class="text-end pe-3">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar carrera?');">
                                            <input type="hidden" name="accion" value="eliminar_carrera">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECCIÓN: MATERIAS -->
            <?php elseif ($seccion === 'materias'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Materias <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Plan de estudios y asignaturas vinculadas por carrera.</p>
                    </div>
                    <button class="btn btn-dark btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalMateria"><i class="bi bi-plus-lg me-1"></i> Nueva Materia</button>
                </div>
                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Código</th>
                                    <th class="py-3">Materia</th>
                                    <th class="py-3">Semestre</th>
                                    <th class="py-3">Carrera Asociada</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos as $m): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $m['id'] ?></td>
                                    <td><code><?= htmlspecialchars($m['codigo']) ?></code></td>
                                    <td><strong><?= htmlspecialchars($m['nombre']) ?></strong></td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary border">Semestre <?= $m['semestre'] ?></span></td>
                                    <td><?= htmlspecialchars($m['carrera']) ?></td>
                                    <td class="text-end pe-3">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar materia?');">
                                            <input type="hidden" name="accion" value="eliminar_materia">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECCIÓN: TUTORES -->
            <?php elseif ($seccion === 'tutores'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Tutores Académicos <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Docentes y tutores encargados de guiar a los estudiantes (50 tutores registrados).</p>
                    </div>
                    <button class="btn btn-dark btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTutor"><i class="bi bi-plus-lg me-1"></i> Nuevo Tutor</button>
                </div>
                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Nombre</th>
                                    <th class="py-3">Especialidad</th>
                                    <th class="py-3">Correo</th>
                                    <th class="py-3">Teléfono</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos as $t): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $t['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($t['nombre']) ?></strong></td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info border"><?= htmlspecialchars($t['especialidad']) ?></span></td>
                                    <td><?= htmlspecialchars($t['email']) ?></td>
                                    <td><code><?= htmlspecialchars($t['telefono']) ?></code></td>
                                    <td class="text-end pe-3">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar tutor?');">
                                            <input type="hidden" name="accion" value="eliminar_tutor">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECCIÓN: TUTORÍAS -->
            <?php elseif ($seccion === 'tutorias'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Control de Tutorías <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Historial y estado de las sesiones programadas (50 tutorías registradas).</p>
                    </div>
                    <a href="panel.php?seccion=asignar" class="btn btn-dark btn-sm px-3 py-2 fw-semibold"><i class="bi bi-calendar-plus me-1"></i> Programar Tutoría</a>
                </div>
                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Estudiante</th>
                                    <th class="py-3">Tutor</th>
                                    <th class="py-3">Materia</th>
                                    <th class="py-3">Fecha y Hora</th>
                                    <th class="py-3">Estado</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos as $tut): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $tut['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($tut['estudiante']) ?></strong></td>
                                    <td><?= htmlspecialchars($tut['tutor']) ?></td>
                                    <td><?= htmlspecialchars($tut['materia']) ?></td>
                                    <td><code><?= htmlspecialchars($tut['fecha']) ?></code></td>
                                    <td>
                                        <?php 
                                            $badgeEstado = 'bg-success bg-opacity-10 text-success border';
                                            if ($tut['estado'] === 'Pendiente de Confirmación') $badgeEstado = 'bg-warning bg-opacity-10 text-dark border';
                                            if ($tut['estado'] === 'Completada') $badgeEstado = 'bg-primary bg-opacity-10 text-primary border';
                                        ?>
                                        <span class="badge <?= $badgeEstado ?>"><?= htmlspecialchars($tut['estado']) ?></span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar tutoría?');">
                                            <input type="hidden" name="accion" value="eliminar_tutoria">
                                            <input type="hidden" name="id" value="<?= $tut['id'] ?>">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- SECCIÓN: ASIGNAR TUTORÍA -->
            <?php elseif ($seccion === 'asignar'): ?>
                <div class="mb-4">
                    <h3 class="fw-bold text-dark m-0">Asignar Nueva Tutoría</h3>
                    <p class="text-muted small m-0 mt-1">Vincula un estudiante con su tutor y materia correspondiente.</p>
                </div>
                <div class="card card-table p-4" style="max-width: 600px;">
                    <form method="POST">
                        <input type="hidden" name="accion" value="asignar_tutoria">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Estudiante</label>
                            <select name="estudiante" class="form-select" required>
                                <option value="">Seleccione estudiante...</option>
                                <?php foreach ($estudiantes as $est): ?>
                                    <option value="<?= htmlspecialchars($est['nombre']) ?>"><?= htmlspecialchars($est['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tutor</label>
                            <select name="tutor" class="form-select" required>
                                <option value="">Seleccione tutor...</option>
                                <?php foreach ($tutores as $tur): ?>
                                    <option value="<?= htmlspecialchars($tur['nombre']) ?>"><?= htmlspecialchars($tur['nombre']) ?> (<?= htmlspecialchars($tur['especialidad']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Materia</label>
                            <select name="materia" class="form-select" required>
                                <option value="">Seleccione materia...</option>
                                <?php foreach ($materias as $mat): ?>
                                    <option value="<?= htmlspecialchars($mat['nombre']) ?>"><?= htmlspecialchars($mat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Fecha y Hora</label>
                            <input type="datetime-local" name="fecha" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 fw-semibold">Guardar y Asignar Tutoría</button>
                    </form>
                </div>

            <!-- SECCIÓN: TRIBUNALES -->
            <?php elseif ($seccion === 'tribunales'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold text-dark m-0">Tribunales de Grado <span class="badge bg-light text-dark border ms-2 fs-6"><?= count($datos) ?></span></h3>
                        <p class="text-muted small m-0 mt-1">Asignación de tribunales para defensas de proyecto (50 tribunales registrados).</p>
                    </div>
                    <button class="btn btn-dark btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTribunal"><i class="bi bi-plus-lg me-1"></i> Registrar Tribunal</button>
                </div>
                <div class="card card-table overflow-hidden">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase text-muted sticky-top" style="font-size: 0.75rem; background: #f8f9fa; z-index: 10;">
                                <tr>
                                    <th class="py-3 ps-3">ID</th>
                                    <th class="py-3">Estudiante</th>
                                    <th class="py-3">Título del Proyecto</th>
                                    <th class="py-3">Tribunal 1</th>
                                    <th class="py-3">Tribunal 2</th>
                                    <th class="py-3">Fecha Defensa</th>
                                    <th class="py-3 text-end pe-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datos as $tri): ?>
                                <tr>
                                    <td class="ps-3 fw-semibold">#<?= $tri['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($tri['estudiante']) ?></strong></td>
                                    <td><?= htmlspecialchars($tri['proyecto']) ?></td>
                                    <td><?= htmlspecialchars($tri['tribunal_1']) ?></td>
                                    <td><?= htmlspecialchars($tri['tribunal_2']) ?></td>
                                    <td><span class="badge bg-warning bg-opacity-10 text-dark border"><?= htmlspecialchars($tri['fecha']) ?></span></td>
                                    <td class="text-end pe-3">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar tribunal?');">
                                            <input type="hidden" name="accion" value="eliminar_tribunal">
                                            <input type="hidden" name="id" value="<?= $tri['id'] ?>">
                                            <button type="submit" class="btn btn-light btn-sm text-danger border"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODALES -->
    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="crear_usuario">
                <div class="modal-header"><h5 class="modal-title fw-bold">Nuevo Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nombre Completo</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Correo Electrónico</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Usuario (Login)</label><input type="text" name="usuario" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Contraseña</label><input type="password" name="password" class="form-control" value="12345" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Rol Asignado</label><select name="rol" class="form-select"><option value="Estudiante">Estudiante</option><option value="Tutor">Tutor</option><option value="Administrador">Administrador</option></select></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Estado</label><select name="estado" class="form-select"><option value="Activo">Activo</option><option value="Inactivo">Inactivo</option></select></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Guardar Usuario</button></div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="editar_usuario">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-header"><h5 class="modal-title fw-bold">Editar Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nombre Completo</label><input type="text" name="nombre" id="edit-nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Correo Electrónico</label><input type="email" name="email" id="edit-email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Usuario (Login)</label><input type="text" name="usuario" id="edit-usuario" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Nueva Contraseña (opcional)</label><input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener la actual"></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Rol Asignado</label><select name="rol" id="edit-rol" class="form-select"><option value="Estudiante">Estudiante</option><option value="Tutor">Tutor</option><option value="Administrador">Administrador</option></select></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Estado</label><select name="estado" id="edit-estado" class="form-select"><option value="Activo">Activo</option><option value="Inactivo">Inactivo</option></select></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Actualizar Usuario</button></div>
            </form>
        </div>
    </div>

    <!-- Modal Carrera -->
    <div class="modal fade" id="modalCarrera" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="crear_carrera">
                <div class="modal-header"><h5 class="modal-title fw-bold">Nueva Carrera</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nombre de la Carrera</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Código</label><input type="text" name="codigo" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Facultad</label><input type="text" name="facultad" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Guardar Carrera</button></div>
            </form>
        </div>
    </div>

    <!-- Modal Materia -->
    <div class="modal fade" id="modalMateria" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="crear_materia">
                <div class="modal-header"><h5 class="modal-title fw-bold">Nueva Materia</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nombre de la Materia</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Código</label><input type="text" name="codigo" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Semestre</label><input type="number" name="semestre" class="form-control" min="1" max="10" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Carrera</label><input type="text" name="carrera" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Guardar Materia</button></div>
            </form>
        </div>
    </div>

    <!-- Modal Tutor -->
    <div class="modal fade" id="modalTutor" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="crear_tutor">
                <div class="modal-header"><h5 class="modal-title fw-bold">Nuevo Tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Nombre Completo</label><input type="text" name="nombre" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Especialidad</label><input type="text" name="especialidad" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Correo Electrónico</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Teléfono</label><input type="text" name="telefono" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Guardar Tutor</button></div>
            </form>
        </div>
    </div>

    <!-- Modal Tribunal -->
    <div class="modal fade" id="modalTribunal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <input type="hidden" name="accion" value="crear_tribunal">
                <div class="modal-header"><h5 class="modal-title fw-bold">Registrar Tribunal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label small fw-semibold">Estudiante</label><input type="text" name="estudiante" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Título del Proyecto</label><input type="text" name="proyecto" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Tribunal 1</label><input type="text" name="tribunal_1" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Tribunal 2</label><input type="text" name="tribunal_2" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label small fw-semibold">Fecha de Defensa</label><input type="date" name="fecha" class="form-control" required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-dark w-100">Registrar Tribunal</button></div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const editButtons = document.querySelectorAll('.btn-editar');
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('edit-id').value = this.getAttribute('data-id');
                document.getElementById('edit-nombre').value = this.getAttribute('data-nombre');
                document.getElementById('edit-email').value = this.getAttribute('data-email');
                document.getElementById('edit-usuario').value = this.getAttribute('data-usuario');
                document.getElementById('edit-rol').value = this.getAttribute('data-rol');
                document.getElementById('edit-estado').value = this.getAttribute('data-estado');
            });
        });
    </script>
</body>
</html>