<?php
if (session_status() === PHP_SESSION_NONE) { @session_start(); }
$rol = $_SESSION["rol"] ?? $_SESSION["usuario_rol"] ?? "estudiante";
$nombre = $_SESSION["nombre"] ?? $_SESSION["usuario_nombre"] ?? "Usuario";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Sistema de Tutorías Académicas</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Header / Navbar -->
    <header class="bg-[#0b192c] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-[#1e3e62] p-2.5 rounded-xl shadow-inner">
                    <i class="fa-solid fa-graduation-cap text-xl text-blue-300"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold tracking-wide">Sistema de Tutorías Académicas</h1>
                    <p class="text-xs text-slate-400">Universidad Privada Domingo Savio</p>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-[#1e3e62]/80 border border-slate-700 px-4 py-2 rounded-full flex items-center space-x-3 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-300 font-bold text-sm">
                        <?php echo strtoupper(substr($nombre, 0, 1)); ?>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-white"><?php echo htmlspecialchars($nombre); ?></span>
                        <span class="block text-[10px] text-blue-300 uppercase tracking-wider font-medium"><?php echo htmlspecialchars($rol); ?></span>
                    </div>
                </div>
                <a href="views/login/login.php" class="bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 px-3.5 py-2 rounded-xl text-sm font-medium transition duration-200 flex items-center space-x-1.5">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Salir</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-8">
        
        <!-- Welcome Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8 relative overflow-hidden">
            <div class="absolute right-0 top-0 bottom-0 w-2 bg-gradient-to-b from-blue-600 to-indigo-600"></div>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wider 
                            <?php 
                                if ($rol === 'administrador') echo 'bg-red-100 text-red-700 border border-red-200';
                                elseif ($rol === 'tutor') echo 'bg-amber-100 text-amber-700 border border-amber-200';
                                else echo 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                            ?>">
                            <i class="fa-solid fa-circle text-[8px] mr-1.5"></i> <?php echo htmlspecialchars($rol); ?>
                        </span>
                        <span class="text-xs text-slate-400 font-medium">• Sesión Activa</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">¡Bienvenido de nuevo, <?php echo htmlspecialchars($nombre); ?>!</h2>
                    <p class="text-slate-600 mt-1 text-sm">Panel de control optimizado para la gestión académica y demostración en vivo.</p>
                </div>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-right hidden lg:block">
                    <span class="block text-xs font-medium text-slate-400">Fecha del Sistema</span>
                    <span class="text-sm font-bold text-slate-700"><?php echo date('d / m / Y'); ?></span>
                </div>
            </div>
        </div>

        <!-- Role-Based Dashboards -->
        <?php if ($rol === "administrador"): ?>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-gauge-high text-blue-600"></i>
                <span>Panel de Administración Global</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-red-500 group">
                    <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Gestión de Usuarios</h4>
                    <p class="text-slate-500 text-sm mb-6">Administra cuentas, permisos y roles de estudiantes y tutores.</p>
                    <button onclick="alert('Módulo de gestión de usuarios activo para demostración.');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Administrar</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-blue-500 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Reportes y Estadísticas</h4>
                    <p class="text-slate-500 text-sm mb-6">Analiza las métricas globales de tutorías y horas acumuladas.</p>
                    <button onclick="alert('Generando reporte global de rendimiento...');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Ver Reportes</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-indigo-500 group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Estado de la Base de Datos</h4>
                    <p class="text-slate-500 text-sm mb-6">Conexión activa mediante PDO a MySQL en contenedor Docker.</p>
                    <button onclick="alert('Conexión PDO establecida correctamente y optimizada.');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Ver Estado</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

        <?php elseif ($rol === "tutor"): ?>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-chalkboard-user text-amber-600"></i>
                <span>Panel de Gestión del Tutor</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-amber-500 group">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Mis Tutorías Programadas</h4>
                    <p class="text-slate-500 text-sm mb-6">Consulta la agenda de sesiones asignadas con estudiantes.</p>
                    <button onclick="alert('Cargando agenda de tutorías asignadas...');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Ver Agenda</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-emerald-500 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Configurar Disponibilidad</h4>
                    <p class="text-slate-500 text-sm mb-6">Establece los horarios libres para que los alumnos reserven.</p>
                    <button onclick="alert('Abriendo configuración de horarios y disponibilidad...');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Configurar Horarios</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>

        <?php else: ?>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center space-x-2">
                <i class="fa-solid fa-user-graduate text-emerald-600"></i>
                <span>Panel del Estudiante</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-emerald-500 group">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Buscar Tutores Disponibles</h4>
                    <p class="text-slate-500 text-sm mb-6">Encuentra tutores especializados por materia y solicita asesoría.</p>
                    <button onclick="alert('Buscando tutores activos en el sistema...');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Buscar Tutor</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition duration-200 border-l-4 border-l-blue-500 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition duration-200">
                        <i class="fa-solid fa-bookmark"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900 mb-1">Mis Reservas y Citas</h4>
                    <p class="text-slate-500 text-sm mb-6">Revisa el estado de tus solicitudes de tutoría pendientes y confirmadas.</p>
                    <button onclick="alert('Cargando historial de reservas...');" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-medium py-2.5 px-4 rounded-xl text-sm transition duration-200 flex items-center justify-center space-x-2">
                        <span>Ver Reservas</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-12 py-6 text-center text-xs text-slate-400">
        <p>Sistema de Tutorías Académicas • Universidad Privada Domingo Savio (UPDS) Sede Tarija</p>
    </footer>

</body>
</html>