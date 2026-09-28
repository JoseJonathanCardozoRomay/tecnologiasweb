<?php
/**
 * Listado de Usuarios — 500+ Datos de Prueba FORZADOS
 */
require_once __DIR__ . '/../config/sesion.php';
requerirRol(['administrador']);
require_once __DIR__ . '/../models/UsuarioModel.php';

$modelo = new UsuarioModel();

// ✅ Paginación
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$por_pagina = 20;
$offset = ($pagina - 1) * $por_pagina;

// ✅ Contar total
$conexion = $modelo->getConexion();
$stmt = $conexion->query("SELECT COUNT(*) FROM usuarios");
$total_registros = (int)$stmt->fetchColumn();
$total_paginas = $total_registros > 0 ? (int)ceil($total_registros / $por_pagina) : 1;

// =====================================================
// ✅ GENERAR 500+ USUARIOS — SIN IMPORTAR SI HAY ALGO
// =====================================================
$forzar_generar = true; // Cambiado a true para crearlos ahora

if ($forzar_generar && $total_registros === 0) {
    $nombres = ['Juan','María','Carlos','Ana','Luis','Sofía','Pedro','Marta','Jorge','Lucía','José','Carla','Diego','Elena','Miguel','Paula','Andrés','Gabriela','Fernando','Valeria','Roberto','Daniela','Ángel','Victoria','Francisco','Natalia','David','Carmen','Alejandro','Isabel','Javier','Sara','Manuel','Andrea','Pablo','Marina','Rafael','Claudia','Sergio','Adriana','Héctor','Rosa','Alberto','Silvia','Ricardo','Patricia','Gabriel','Mónica','Cristian','Verónica','Iván','Jimena','Oscar','Lorena','Mario','Erika','Raúl','Nadia','Hugo','Sandra'];
    $apellidos = ['Pérez','Gómez','Rodríguez','Fernández','López','Martínez','Sánchez','Ramírez','Torres','Díaz','Vargas','Castillo','Cruz','Morales','Ortiz','Gutiérrez','Chávez','Mendoza','Ruiz','Hernández','Silva','Bravo','Campos','Figueroa','Cáceres','Rojas','Medina','Castro','Reyes','Jiménez','Vera','Cabrera','Rivera','Domínguez','Carrasco','Araya','Ponce','Soto','Leiva','Contreras','Sepúlveda','Escobar','Peña','Calderón','Fuentes','Navarro','Aguilar','Vega','Molina','Delgado','Benítez','Salinas','Durán','Miranda','Luján','Bustos','Ramos','Guerrero','Paz','Cortés','Acosta'];

    echo "<div style='background:#e8f4fd; padding:15px; margin:10px; border-radius:8px;'>";
    echo "<strong>🔄 Generando 500+ usuarios... Por favor espera ⏳</strong><br>";
    flush();

    // 500 usuarios automáticos
    for ($i = 1; $i <= 500; $i++) {
        $n = $nombres[($i % count($nombres))];
        $a1 = $apellidos[(($i + 10) % count($apellidos))];
        $a2 = $apellidos[(($i + 20) % count($apellidos))];
        $rol = ($i % 3 === 0) ? 3 : (($i % 2 === 0) ? 2 : 2);

        $datos = [
            'id_rol'       => $rol,
            'nombre'       => $n,
            'apellido'     => $a1 . ' ' . $a2,
            'correo'       => 'usuario' . $i . '@upds.edu.bo',
            'usuario'      => 'usuario' . $i,
            'contrasena'   => '123456',
            'telefono'     => '7' . str_pad($i, 7, '0', STR_PAD_LEFT)
        ];
        
        $modelo->crear($datos);
    }

    // Usuario administrador fijo
    $modelo->crear([
        'id_rol'       => 1,
        'nombre'       => 'Admin',
        'apellido'     => 'Sistema',
        'correo'       => 'admin@upds.edu.bo',
        'usuario'      => 'admin',
        'contrasena'   => '123456',
        'telefono'     => '77700000'
    ]);

    echo "<strong>✅ ¡Listo! Se crearon 501 usuarios</strong>";
    echo "</div>";

    // Recargar totales
    $total_registros = 501;
    $total_paginas = (int)ceil($total_registros / $por_pagina);
}

// ✅ Cargar usuarios de la página actual
$usuarios = $modelo->listarPaginado($por_pagina, $offset);

require_once __DIR__ . '/../views/usuarios/listar.php';