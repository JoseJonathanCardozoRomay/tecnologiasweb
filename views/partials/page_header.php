<?php
/**
 * Encabezado de página unificado (todas las vistas del sistema).
 *
 * Variables opcionales:
 *   $titulo        (string) Título visible del encabezado.
 *   $descripcion   (string) Texto secundario bajo el título.
 *   $icono         (string) Clase de Bootstrap Icons, ej: 'bi-people-fill'.
 *   $contador      (int|string|null) Valor mostrado como badge junto al título.
 *   $colorContador (string) Variante de color del badge (primary, success, info...).
 *   $migas         (array)  Migas de pan: [['texto' => ..., 'url' => ..., 'actual' => bool]].
 *   $accion        (string) HTML de los botones de acción (se imprime tal cual).
 *
 * Ejemplo:
 *   $titulo = 'Usuarios del Sistema';
 *   $descripcion = 'Administra las cuentas registradas.';
 *   $icono = 'bi-people-fill';
 *   $contador = $totalRegistros;
 *   $accion = '<a class="btn btn-primary" href="usuarios_crear.php">Nuevo Usuario</a>';
 *   include __DIR__ . '/../partials/page_header.php';
 */
$titulo = $titulo ?? ($tituloPagina ?? '');
$descripcion = $descripcion ?? '';
$icono = $icono ?? '';
$contador = $contador ?? null;
$colorContador = $colorContador ?? 'primary';
$migas = $migas ?? [];
$accion = $accion ?? null;
?>
<header class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
 <div>
    <?php if ($migas): ?>
      <nav aria-label="Migas de pan" class="breadcrumb-nav mb-2">
        <ol class="breadcrumb mb-0">
          <?php foreach ($migas as $miga): ?>
            <li class="breadcrumb-item <?= !empty($miga['actual']) ? 'active' : '' ?>" <?= !empty($miga['actual']) ? 'aria-current="page"' : '' ?>>
              <?php if (!empty($miga['url']) && empty($miga['actual'])): ?>
                <a href="<?= htmlspecialchars($miga['url'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($miga['texto']) ?></a>
              <?php else: ?>
                <?= htmlspecialchars($miga['texto']) ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </nav>
    <?php endif; ?>
        <h1 class="page-title d-flex align-items-center gap-2 mb-1">
          <?php if ($icono !== ''): ?><i class="bi <?= htmlspecialchars($icono, ENT_QUOTES, 'UTF-8') ?> text-primary" aria-hidden="true"></i><?php endif; ?>
          <span><?= htmlspecialchars($titulo) ?></span>
          <?php if ($contador !== null && $contador !== ''): ?>
            <span class="badge bg-<?= htmlspecialchars($colorContador, ENT_QUOTES, 'UTF-8') ?> bg-opacity-10 text-<?= htmlspecialchars($colorContador, ENT_QUOTES, 'UTF-8') ?> fs-6"><?= htmlspecialchars((string) $contador, ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </h1>
        <?php if ($descripcion !== ''): ?><p class="page-description mb-0"><?= htmlspecialchars($descripcion) ?></p><?php endif; ?>
     </div>
      <?php if ($accion): ?>
        <div class="page-action d-flex flex-wrap gap-2"><?= $accion ?></div>
      <?php endif; ?>
    </header>
    <?php
    // Limpieza: evita que las variables se filtren a las siguientes inclusiones.
    unset($titulo, $descripcion, $icono, $contador, $colorContador, $migas, $accion);
    ?>
