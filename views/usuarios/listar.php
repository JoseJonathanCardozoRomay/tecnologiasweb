<?php
require_once __DIR__ . '/../../includes/verificar_sesion.php';
$tituloPagina = 'Gestión de Usuarios - Sistema de Tutorías';
include __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../includes/funciones.php';
mostrarFlash();

/** Agrupa las cuentas por rol para mostrarlas en pestañas independientes. */
$usuariosPorRol = ['estudiante' => [], 'tutor' => [], 'administrador' => []];
foreach ($usuarios as $u) {
    $rol = $u['nombre_rol'] ?? '';
    if (isset($usuariosPorRol[$rol])) {
        $usuariosPorRol[$rol][] = $u;
    }
}
$etiquetasRol = [
    'estudiante' => ['Estudiantes', 'bi-mortarboard', 'badge-estudiante'],
    'tutor' => ['Tutores', 'bi-person-video3', 'badge-tutor'],
    'administrador' => ['Administradores', 'bi-shield-lock', 'badge-admin'],
];
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold mb-1 d-flex align-items-center gap-2"><i class="bi bi-people-fill text-primary"></i><span>Usuarios del Sistema</span><span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?=count($usuarios)?></span></h2>
    <p class="text-muted mb-0">Cuentas organizadas por rol para facilitar la administración.</p>
  </div>
  <a href="usuarios_crear.php" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-3 py-2 rounded-3"><i class="bi bi-person-plus-fill"></i><span class="fw-semibold">Nuevo Usuario</span></a>
</div>
<div class="card card-custom shadow-sm overflow-hidden">
  <div class="card-header bg-white border-0 p-3">
    <div class="input-group mb-3" style="max-width:360px;"><span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span><input type="text" id="buscadorUsuarios" class="form-control bg-light border-start-0" placeholder="Buscar por nombre, usuario, correo..."></div>
    <ul class="nav nav-tabs border-0" id="rolesUsuarios" role="tablist">
      <?php $primera=true; foreach($etiquetasRol as $rol => [$label,$icon,$badge]): ?>
        <li class="nav-item" role="presentation"><button class="nav-link js-rol-tab <?=$primera?'active':''?> fw-semibold" data-rol-tab="tab-<?=e($rol)?>" type="button" role="tab" aria-selected="<?=$primera?'true':'false'?>"><i class="bi <?=e($icon)?> me-1"></i><?=e($label)?> <span class="badge rounded-pill text-bg-light border ms-1"><?=count($usuariosPorRol[$rol])?></span></button></li>
      <?php $primera=false; endforeach; ?>
    </ul>
  </div>
  <div class="tab-content" id="contenidoRolesUsuarios">
    <?php $primera=true; foreach($usuariosPorRol as $rol=>$lista): ?>
      <div class="tab-pane fade <?=$primera?'show active':''?>" id="tab-<?=e($rol)?>" role="tabpanel">
        <div class="table-responsive"><table class="table table-hover align-middle mb-0 tabla-rol-usuarios"><thead class="table-light text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.5px;"><tr><th class="ps-4">Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th>Fecha Registro</th><th class="text-end pe-4">Acciones</th></tr></thead><tbody>
        <?php foreach($lista as $u): ?>
          <?php $badgeRol=$etiquetasRol[$rol][2]; ?>
          <tr class="fila-usuario">
            <td class="ps-4"><div class="d-flex align-items-center gap-3"><div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary fw-bold" style="width:40px;height:40px;font-size:.9rem;"><?=e(iniciales((string)$u['nombre'], (string)$u['apellido']))?></div><div><div class="fw-bold text-dark"><?=e($u['nombre'].' '.$u['apellido'])?></div><small class="text-muted"><i class="bi bi-person me-1"></i><?=e($u['usuario'])?></small></div></div></td>
            <td><span class="text-secondary"><?=e($u['correo'])?></span></td>
            <td><span class="badge rounded-pill px-3 py-1 text-capitalize <?=e($badgeRol)?>"><?=e($u['nombre_rol'])?></span></td>
            <td><?php if($u['estado']==='activo'): ?><span class="badge bg-success bg-opacity-10 text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Activo</span><?php else: ?><span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary-subtle px-2 py-1"><i class="bi bi-dash-circle me-1"></i>Inactivo</span><?php endif; ?></td>
            <td class="text-muted small"><?=date('d/m/Y',strtotime($u['fecha_registro']))?></td>
            <td class="text-end pe-4"><div class="btn-group" role="group"><a href="usuarios_editar.php?id=<?=$u['id_usuario']?>" class="btn btn-outline-primary btn-sm" title="Editar"><i class="bi bi-pencil-fill"></i></a><form method="POST" action="usuarios_eliminar.php" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')"><?=csrfField()?><input type="hidden" name="id" value="<?=$u['id_usuario']?>"><button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></button></form></div></td>
          </tr>
        <?php endforeach; ?>
        <tr class="sin-resultados <?=empty($lista)?'':'d-none'?>"><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>No hay usuarios en este rol.</td></tr>
        </tbody></table></div>
      </div>
    <?php $primera=false; endforeach; ?>
  </div>
</div>
<script>
const buscadorUsuarios=document.getElementById('buscadorUsuarios');
function filtrarUsuarios(){const valor=(buscadorUsuarios?.value||'').toLowerCase().trim();document.querySelectorAll('.tabla-rol-usuarios').forEach(tabla=>{let visibles=0;tabla.querySelectorAll('tbody .fila-usuario').forEach(fila=>{const coincide=fila.textContent.toLowerCase().includes(valor);fila.style.display=coincide?'':'none';if(coincide)visibles++;});const vacio=tabla.querySelector('.sin-resultados');if(vacio)vacio.classList.toggle('d-none',visibles>0);});}
buscadorUsuarios?.addEventListener('input',filtrarUsuarios);

// Las pestañas se gestionan también con JavaScript propio para que el cambio
// de rol funcione incluso si Bootstrap no ha cargado todavía o si el CDN
// externo está temporalmente bloqueado. Solo una pestaña queda activa a la vez.
document.querySelectorAll('.js-rol-tab').forEach((boton) => {
  boton.addEventListener('click', () => {
    const objetivo = boton.dataset.rolTab;
    if (!objetivo) return;

    document.querySelectorAll('.js-rol-tab').forEach((b) => {
      const activa = b === boton;
      b.classList.toggle('active', activa);
      b.setAttribute('aria-selected', activa ? 'true' : 'false');
    });

    document.querySelectorAll('#contenidoRolesUsuarios .tab-pane').forEach((panel) => {
      const mostrar = panel.id === objetivo;
      panel.classList.toggle('show', mostrar);
      panel.classList.toggle('active', mostrar);
    });
  });
});
</script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>
