</main>
<?php require_once __DIR__ . '/../../includes/csrf.php'; ?>

<footer class="app-footer text-center">
  Universidad Privada Domingo Savio — Sede Tarija · Tecnologías Web · <?= date('Y') ?>
</footer>
<?php if ($esEstudiante): ?></div><?php else: ?></div></div><?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/assets/js/select2-init.js"></script>
<script>
  // =========================================================
  // Toasts (notificaciones flotantes en español)
  // =========================================================
  // Tipos admitidos: success, danger, warning, info.
  // Uso desde JavaScript: mostrarToast('Guardado con éxito', 'success');
  const TOAST_CONFIG = {
    success: { icono: 'bi-check-circle-fill', titulo: 'Éxito' },
    danger:  { icono: 'bi-exclamation-octagon-fill', titulo: 'Error' },
    warning: { icono: 'bi-exclamation-triangle-fill', titulo: 'Atención' },
    info:    { icono: 'bi-info-circle-fill', titulo: 'Información' }
  };
  function mostrarToast(mensaje, tipo = 'info', opciones = {}) {
    const config = TOAST_CONFIG[tipo] || TOAST_CONFIG.info;
    let contenedor = document.getElementById('contenedor-toasts');
    if (!contenedor) {
      contenedor = document.createElement('div');
      contenedor.id = 'contenedor-toasts';
      contenedor.className = 'toast-container position-fixed top-0 end-0 p-3';
      contenedor.style.zIndex = '1090';
      document.body.appendChild(contenedor);
    }
    const toast = document.createElement('div');
    toast.className = 'toast toast-upds toast-' + tipo;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.dataset.bsDelay = String(opciones.retardo ?? 6000);
    toast.dataset.bsAutohide = 'true';
    const crearTexto = (contenido) => document.createTextNode(contenido);
    const encabezado = document.createElement('div');
    encabezado.className = 'toast-header';
    const icono = document.createElement('i');
    icono.className = 'bi ' + config.icono + ' me-2 text-' + tipo;
    const titulo = document.createElement('strong');
    titulo.className = 'me-auto';
    titulo.textContent = opciones.titulo || config.titulo;
    const cerrar = document.createElement('button');
    cerrar.type = 'button';
    cerrar.className = 'btn-close';
    cerrar.setAttribute('data-bs-dismiss', 'toast');
    cerrar.setAttribute('aria-label', 'Cerrar');
    encabezado.append(icono, titulo, cerrar);
    const cuerpo = document.createElement('div');
    cuerpo.className = 'toast-body';
    cuerpo.appendChild(crearTexto(mensaje));
    toast.append(encabezado, cuerpo);
    contenedor.appendChild(toast);
    const instancia = bootstrap.Toast.getOrCreateInstance(toast);
    toast.addEventListener('hidden.bs.toast', () => toast.remove());
    instancia.show();
    return instancia;
  }
  window.mostrarToast = mostrarToast;

  // Muestra al cargar los toasts generados en el servidor (mensajes flash).
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#contenedor-toasts .toast').forEach((toast) => {
      bootstrap.Toast.getOrCreateInstance(toast).show();
    });
  });

  function enviarPostSeguro(url, extras = {}) {
    const destino = new URL(url, window.location.href);
    const formulario = document.createElement('form');
    formulario.method = 'POST';
    formulario.action = destino.pathname;
    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = 'csrf_token';
    token.value = '<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>';
    formulario.appendChild(token);
    destino.searchParams.forEach((valor, clave) => {
      const campo = document.createElement('input');
      campo.type = 'hidden'; campo.name = clave; campo.value = valor;
      formulario.appendChild(campo);
    });
    Object.entries(extras).forEach(([clave, valor]) => {
      const campo = document.createElement('input');
      campo.type = 'hidden'; campo.name = clave; campo.value = valor;
      formulario.appendChild(campo);
    });
    document.body.appendChild(formulario);
    formulario.submit();
  }
  function cerrarSesion(event) {
    if (event) event.preventDefault();
    enviarPostSeguro('/controllers/logout.php');
  }
  function confirmarEliminacion(url, mensaje = '¿Estás seguro de eliminar este registro?') {
    Swal.fire({
      title: '¿Confirmar eliminación?',
      text: mensaje,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#b42318',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      customClass: { popup: 'swal-upds-popup' }
    }).then((result) => {
      if (result.isConfirmed) enviarPostSeguro(url);
    });
  }
  function confirmarDetencion(url) {
    Swal.fire({
      title: 'Detener la sesión',
      text: 'La sesión quedará detenida y no podrá reanudarse. ¿Deseas continuar?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#6c757d',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Sí, detener',
      cancelButtonText: 'Volver',
      customClass: { popup: 'swal-upds-popup' }
    }).then((result) => {
      if (result.isConfirmed) enviarPostSeguro(url);
    });
  }
  function confirmarCancelacion(url) {
    Swal.fire({
      title: 'Motivo de la cancelación',
      input: 'text',
      inputPlaceholder: 'Escribe el motivo (mínimo 5 caracteres)',
      inputAttributes: { maxlength: 255 },
      showCancelButton: true,
      confirmButtonColor: '#b42318',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Cancelar tutoría',
      cancelButtonText: 'Volver',
      customClass: { popup: 'swal-upds-popup' },
      inputValidator: (valor) => {
        if (!valor || valor.trim().length < 5) {
          return 'El motivo es obligatorio (mínimo 5 caracteres).';
        }
      }
    }).then((result) => {
      if (result.isConfirmed) enviarPostSeguro(url, { motivo: result.value.trim() });
    });
  }
  function confirmarTransicion(url, mensaje, tipo = 'info') {
    Swal.fire({
      icon: tipo,
      title: mensaje,
      showCancelButton: true,
      confirmButtonColor: tipo === 'warning' ? '#f59e0b' : '#002b49',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Confirmar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      customClass: { popup: 'swal-upds-popup' }
    }).then((result) => {
      if (result.isConfirmed) enviarPostSeguro(url);
    });
  }
</script>
</body>
</html>
