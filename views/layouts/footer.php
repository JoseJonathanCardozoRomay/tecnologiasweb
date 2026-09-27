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
  function escaparHtml(texto) {
    const div = document.createElement('div');
    div.textContent = texto == null ? '' : String(texto);
    return div.innerHTML;
  }
  function confirmarFinalizacion(url, estados) {
    const lista = Array.isArray(estados) ? estados : [];
    if (lista.length === 0) {
      Swal.fire({
        icon: 'error',
        title: 'Sin resultados de cierre configurados',
        text: 'Comunícate con el administrador: no hay estados de conclusión de Modalidad de Grado registrados.',
        confirmButtonColor: '#002b49'
      });
      return;
    }

    const opciones = lista.map((estado) => {
      const id = escaparHtml(estado.id_estado_conclusion);
      const nombre = escaparHtml(estado.nombre || 'Sin nombre');
      const descripcion = escaparHtml(estado.descripcion || '');
      return `
        <label class="swal-estado" for="swal-estado-${id}">
          <input type="radio" name="swal-estado-conclusion" id="swal-estado-${id}" value="${id}">
          <span class="swal-estado-cuerpo">
            <span class="swal-estado-nombre">${nombre}</span>
            ${descripcion ? `<span class="swal-estado-descripcion">${descripcion}</span>` : ''}
          </span>
          <span class="swal-estado-check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
        </label>`;
    }).join('');

    Swal.fire({
      title: 'Finalizar Modalidad de Grado',
      html: `
        <p class="swal-descripcion">Selecciona el resultado de cierre del expediente de grado. Esta acción no se puede deshacer.</p>
        <div class="swal-estados" role="radiogroup" aria-label="Resultado de cierre">${opciones}</div>`,
      showCancelButton: true,
      confirmButtonColor: '#002b49',
      cancelButtonColor: '#64748b',
      confirmButtonText: 'Finalizar',
      cancelButtonText: 'Volver',
      reverseButtons: true,
      focusConfirm: false,
      customClass: { popup: 'swal-upds-popup', htmlContainer: 'swal-estados-container' },
      preConfirm: () => {
        const seleccion = Swal.getPopup().querySelector('input[name="swal-estado-conclusion"]:checked');
        if (!seleccion) {
          Swal.showValidationMessage('Debes seleccionar un resultado de cierre.');
          return false;
        }
        return seleccion.value;
      }
    }).then((result) => {
      if (result.isConfirmed) enviarPostSeguro(url, { id_estado_conclusion: result.value });
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

  // =========================================================
  // Campana de notificaciones (lectura periódica cada 30 s)
  // =========================================================
  const notifBotones = document.querySelectorAll('.notif-bell-btn');
  const INTERVALO_NOTIFICACIONES = 30000;

  function renderizarNotificaciones(datos) {
    const total = parseInt(datos.no_leidas ?? 0, 10);
    document.querySelectorAll('.notif-bell-btn').forEach((boton) => {
      const contador = boton.querySelector('.notif-count');
      if (!contador) return;
      contador.textContent = total;
      contador.style.display = total > 0 ? '' : 'none';
      boton.classList.toggle('notif-hay', total > 0);
    });
    document.querySelectorAll('.notif-count-label').forEach((label) => {
      label.textContent = total > 0 ? ('(' + total + ' pendientes)') : '';
    });
    document.querySelectorAll('.notif-items').forEach((contenedor) => {
      const items = datos.items ?? [];
      if (items.length === 0) {
        contenedor.innerHTML = '<div class="text-center py-4 text-muted"><i class="bi bi-bell-slash d-block mb-1"></i>Sin notificaciones</div>';
        return;
      }
      contenedor.innerHTML = '';
      items.forEach((notif) => {
        const fila = document.createElement(notif.enlace ? 'a' : 'div');
        fila.className = 'notif-item' + (notif.leida ? ' notif-item-leida' : '');
        if (notif.enlace) {
          fila.href = notif.enlace;
          fila.addEventListener('click', (ev) => {
            if (!notif.leida) {
              ev.preventDefault();
              ev.stopPropagation();
              enviarPostSeguro('/controllers/notificaciones_marcar.php', { id_notificacion: notif.id, destino: notif.enlace });
            }
          });
        }
        const cuerpo = document.createElement('div');
        cuerpo.className = 'd-flex flex-column flex-grow-1 min-w-0';
        const texto = document.createElement('span');
        texto.className = 'notif-text';
        texto.textContent = notif.mensaje;
        const fecha = document.createElement('small');
        fecha.className = 'text-muted';
        fecha.textContent = notif.fecha;
        cuerpo.append(texto, fecha);
        if (!notif.leida) {
          const punto = document.createElement('span');
          punto.className = 'notif-punto';
          fila.append(punto);
        }
        fila.append(cuerpo);
        contenedor.appendChild(fila);
      });
    });
  }

  function consultarNotificaciones() {
    const hayBell = document.querySelector('.notif-bell-btn');
    if (!hayBell) return;
    fetch('/controllers/notificaciones_consultar.php', {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
      credentials: 'same-origin'
    })
      .then((respuesta) => {
        if (!respuesta.ok || !respuesta.headers.get('content-type')?.includes('application/json')) {
          throw new Error('Respuesta no JSON');
        }
        return respuesta.json();
      })
      .then(renderizarNotificaciones)
      .catch(() => {});
  }

  if (notifBotones.length > 0) {
    consultarNotificaciones();
    setInterval(consultarNotificaciones, INTERVALO_NOTIFICACIONES);
  }

  // =========================================================
  // Sidebar colapsable (solo escritorio: lg y superiores)
  // =========================================================
  const CLASE_COLAPSADO = 'sidebar-colapsado';
  const btnToggle = document.getElementById('btnSidebarToggle');
  function aplicarEstadoSidebar() {
    try {
      const colapsado = localStorage.getItem('upds_sidebar_colapsado') === '1';
      document.body.classList.toggle(CLASE_COLAPSADO, colapsado);
    } catch (e) { /* almacenamiento no disponible */ }
  }
  if (btnToggle) {
    btnToggle.addEventListener('click', () => {
      const colapsado = document.body.classList.toggle(CLASE_COLAPSADO);
      try { localStorage.setItem('upds_sidebar_colapsado', colapsado ? '1' : '0'); } catch (e) {}
    });
  }
  if (!<?= $esEstudiante ? 'true' : 'false' ?>) aplicarEstadoSidebar();
</script>
</body>
</html>
