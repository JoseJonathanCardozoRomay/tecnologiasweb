</main>

<footer class="app-footer text-center">
  Universidad Privada Domingo Savio — Sede Tarija · Tecnologías Web · <?= date('Y') ?>
</footer>
<?php if ($esEstudiante): ?></div><?php else: ?></div></div><?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
      if (result.isConfirmed) window.location.href = url;
    });
  }
</script>
</body>
</html>
