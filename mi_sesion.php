<?php
session_start();
echo "<h2>🔍 Datos de tu sesión actual:</h2>";
echo "<p><strong>id_usuario:</strong> " . ($_SESSION['id_usuario'] ?? '❌ NO ESTÁ DEFINIDO') . "</p>";
echo "<p><strong>Nombre:</strong> " . ($_SESSION['usuario_nombre'] ?? 'NO ESTÁ') . "</p>";
echo "<p><strong>Rol:</strong> " . ($_SESSION['rol_nombre'] ?? 'NO ESTÁ') . "</p>";