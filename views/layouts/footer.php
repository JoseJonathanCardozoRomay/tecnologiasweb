</main>
<?php if (($rolSesion ?? '') !== 'estudiante'): ?></div></div><?php else: ?></div><?php endif; ?>
<footer class="bg-white border-top py-3 mt-auto"><div class="container-fluid px-3 px-lg-4 text-center text-muted small"><strong>Sistema Web de Apoyo Académico para Tutorías</strong> &bull; &copy; <?= date('Y') ?> UPDS - Tecnologías Web</div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="/assets/js/select2-init.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<script>
(function(){
  // Select2 se conserva para compatibilidad con formularios heredados del proyecto base.
  if (window.jQuery) {
    window.jQuery('.select2-enabled').select2({width:'100%', language:'es'});
  }
  // Tom Select se reserva para listas largas marcadas explícitamente con .tom-select.
  document.querySelectorAll('select.tom-select').forEach(function(el){
    if (!el.tomselect) new TomSelect(el,{plugins:{remove_button:{title:'Quitar'}}});
  });
  document.querySelectorAll('.toast').forEach(function(el){ new bootstrap.Toast(el).show(); });
})();
function cerrarSesion(event){
  event.preventDefault();
  const href=event.currentTarget.href;
  if(window.Swal){Swal.fire({title:'Cerrar sesión',text:'¿Deseas salir del sistema?',icon:'question',showCancelButton:true,confirmButtonText:'Sí, salir',cancelButtonText:'Volver',reverseButtons:true,customClass:{popup:'swal-upds-popup'}}).then(r=>{if(r.isConfirmed) window.location.href=href;});}
  else window.location.href=href;
}
function enviarPostSeguro(url, extra={}){
  const f=document.createElement('form');f.method='POST';f.action=url;
  const csrf=document.querySelector('input[name="csrf_token"]');
  if(csrf){const i=document.createElement('input');i.type='hidden';i.name='csrf_token';i.value=csrf.value;f.appendChild(i);}
  Object.entries(extra).forEach(([k,v])=>{const i=document.createElement('input');i.type='hidden';i.name=k;i.value=v;f.appendChild(i);});
  document.body.appendChild(f);f.submit();
}
function confirmarEliminacion(url,mensaje='¿Estás seguro de eliminar este registro?'){
  Swal.fire({title:'¿Confirmar eliminación?',text:mensaje,icon:'warning',showCancelButton:true,confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar',customClass:{popup:'swal-upds-popup'}}).then(r=>{if(r.isConfirmed) enviarPostSeguro(url);});
}
function confirmarCancelacion(url){
  Swal.fire({title:'Motivo de la cancelación',input:'text',inputPlaceholder:'Escribe el motivo (mínimo 5 caracteres)',inputAttributes:{maxlength:500},showCancelButton:true,confirmButtonText:'Cancelar tutoría',cancelButtonText:'Volver',reverseButtons:true,customClass:{popup:'swal-upds-popup'},inputValidator:v=>!v||v.trim().length<5?'El motivo es obligatorio (mínimo 5 caracteres).':undefined}).then(r=>{if(r.isConfirmed) enviarPostSeguro(url,{motivo:r.value.trim()});});
}
</script>
</body></html>
