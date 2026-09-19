</main>
<?php require_once __DIR__ . '/../../includes/csrf.php'; ?>

<footer class="app-footer text-center">
  Universidad Privada Domingo Savio — Sede Tarija · Tecnologías Web · <?= date('Y') ?>
</footer>
<?php if ($esEstudiante): ?></div><?php else: ?></div></div><?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</script>
</body>
</html>
