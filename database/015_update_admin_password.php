<?php
// =====================================================================
// ACTUALIZACION DE LA CREDENCIAL DEL USUARIO ADMINISTRADOR
// ---------------------------------------------------------------------
//  Alternativa idempotente a database/012_admin_password.sql: regenera
//  el hash bcrypt en cada ejecucion (sal nueva) y lo aplica sobre la
//  cuenta de administracion.
//
//  Ejecutar dentro del contenedor web:
//      docker exec tutorias_web php /var/www/html/database/015_update_admin_password.php
//
//  La contrasena nunca se escribe en la base de datos: solo su hash
//  bcrypt producido por password_hash() de PHP.
// =====================================================================

require_once __DIR__ . '/../config/conexion.php';

const CLAVE_ADMIN      = '@g4t.a56';
const USUARIO_ADMIN    = 'admin';
const CORREO_ADMIN     = 'admin@sistema-tutorias.edu.bo';

$hash = password_hash(CLAVE_ADMIN, PASSWORD_BCRYPT);

if (!is_string($hash) || !password_verify(CLAVE_ADMIN, $hash)) {
    fwrite(STDERR, "No se pudo generar un hash bcrypt valido.\n");
    exit(1);
}

$stmt = $pdo->prepare(
    "UPDATE usuarios
        SET contrasena_hash = :hash
      WHERE usuario = :usuario
         OR correo  = :correo"
);
$stmt->execute([
    ':hash'    => $hash,
    ':usuario' => USUARIO_ADMIN,
    ':correo'  => CORREO_ADMIN,
]);

$verificacion = $pdo->prepare(
    "SELECT id_usuario, usuario, correo, estado, contrasena_hash
       FROM usuarios
      WHERE usuario = :usuario
         OR correo  = :correo"
);
$verificacion->execute([
    ':usuario' => USUARIO_ADMIN,
    ':correo'  => CORREO_ADMIN,
]);
$admin = $verificacion->fetch();

echo "\n=== CREDENCIAL DEL ADMINISTRADOR ACTUALIZADA ===\n";
if (!$admin) {
    echo "  [ERROR] No existe el usuario administrador.\n\n";
    exit(1);
}
printf("  id_usuario : %d\n", $admin['id_usuario']);
printf("  usuario    : %s\n", $admin['usuario']);
printf("  correo     : %s\n", $admin['correo']);
printf("  algoritmo  : %s\n", substr($admin['contrasena_hash'], 0, 4));
printf("  longitud   : %d\n", strlen($admin['contrasena_hash']));
printf("  password_verify() : %s\n", password_verify(CLAVE_ADMIN, $admin['contrasena_hash']) ? 'OK' : 'FALLA');
echo "\n";
