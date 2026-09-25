<?php
if (!isset($tutores)) $tutores = [];
if (!isset($rol_actual)) $rol_actual = $_SESSION['rol_nombre'] ?? '';
if (!isset($id_usuario_actual)) $id_usuario_actual = $_SESSION['id_usuario'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Tutores</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', serif; }
        body {
            background: linear-gradient(rgba(0, 38, 77, 0.88), rgba(0, 38, 77, 0.88)),
                        url('https://www.upds.edu.bo/wp-content/uploads/2023/07/4.jpg') center/cover no-repeat fixed;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .contenedor {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }
        .volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        h1 {
            color: #003366;
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ffc107;
        }
        .btn-nuevo {
            display: inline-block;
            background: #0066cc;
            color: white;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .tarjeta-tutor {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            background: #f9fbff;
            transition: all 0.3s ease;
        }
        .tarjeta-tutor:hover {
            box-shadow: 0 5px 15px rgba(0,102,204,0.15);
            border-color: #0066cc;
        }
        .tutor-nombre {
            font-size: 18px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }
        .tutor-especialidad {
            color: #0066cc;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .tutor-bio {
            color: #555;
            font-size: 14px;
            margin-bottom: 12px;
            font-style: italic;
        }
        .etiqueta-materia {
            display: inline-block;
            background: #e6f0ff;
            color: #003366;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin: 2px;
        }
        .editar {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 8px;
            margin-top: 10px;
        }
        .editar:hover { background: #218838; }
        .eliminar {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            cursor: pointer;
            margin-top: 10px;
        }
        .eliminar:hover { background: #c82333; }
        .vacio {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .yo-etiqueta {
            display: inline-block;
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <a href="index.php" class="volver">← Volver al inicio</a>
        
        <h1>👨‍🏫 
            <?php if ($rol_actual === 'estudiante'): ?>
                Tutores Disponibles
            <?php elseif ($rol_actual === 'tutor'): ?>
                Mi Perfil de Tutor
            <?php else: ?>
                Gestión de Tutores
            <?php endif; ?>
        </h1>

        <?php if ($rol_actual === 'administrador'): ?>
            <a href="index.php?accion=tutor_crear" class="btn-nuevo">+ Nuevo Tutor</a>
        <?php endif; ?>

        <?php if (empty($tutores)): ?>
            <p class="vacio">
                <?php if ($rol_actual === 'estudiante'): ?>
                    No hay tutores registrados disponibles en este momento.
                <?php else: ?>
                    No hay tutores registrados.
                    <?php if ($rol_actual === 'administrador'): ?>
                        <br><strong>Usa el botón "Nuevo Tutor" para agregar uno.</strong>
                    <?php endif; ?>
                <?php endif; ?>
            </p>
        <?php else: ?>
            <?php foreach ($tutores as $t): 
                $es_su_perfil = ($rol_actual === 'tutor' && ($t['id_usuario'] ?? 0) == $id_usuario_actual);
            ?>
            <?php if ($rol_actual === 'tutor' && !$es_su_perfil) continue; ?>
            
            <div class="tarjeta-tutor">
                <div class="tutor-nombre">
                    <?= htmlspecialchars(($t['nombre'] ?? '') . ' ' . ($t['apellido'] ?? '')) ?>
                    <?php if ($es_su_perfil): ?>
                        <span class="yo-etiqueta">TÚ</span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($t['especialidad'])): ?>
                    <div class="tutor-especialidad">📌 <?= htmlspecialchars($t['especialidad']) ?></div>
                <?php endif; ?>
                
                <?php if (!empty($t['biografia'])): ?>
                    <div class="tutor-bio">"<?= htmlspecialchars($t['biografia']) ?>"</div>
                <?php endif; ?>
                
                <?php if (!empty($t['materias'])): ?>
                    <div>
                        <strong>Materias que domina:</strong><br>
                        <?php foreach ($t['materias'] as $m): ?>
                            <span class="etiqueta-materia"><?= htmlspecialchars($m['nombre_materia']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($rol_actual === 'administrador' || ($rol_actual === 'tutor' && $es_su_perfil)): ?>
                    <div>
                        <a href="index.php?accion=tutor_editar&id=<?= $t['id_tutor'] ?>" class="editar">✏️ Editar</a>
                        <?php if ($rol_actual === 'administrador'): ?>
                            <a href="index.php?accion=tutor_eliminar&id=<?= $t['id_tutor'] ?>" 
                               class="eliminar"
                               onclick="return confirm('¿Seguro que quieres eliminar este tutor?')">🗑️ Eliminar</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>