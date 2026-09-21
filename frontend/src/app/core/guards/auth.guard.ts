import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth.service';

export const authGuard: CanActivateFn = (route, state) => {
  const authService = inject(AuthService);
  const router = inject(Router);

  const sesion = authService.obtenerSesion();

  if (!sesion) {
    router.navigate(['/login'], { queryParams: { redirect: state.url } });
    return false;
  }

  const rolesPermitidos = (route.data?.['roles'] as string[] | undefined) ?? [];
  if (rolesPermitidos.length > 0 && !rolesPermitidos.includes(sesion.rol)) {
    router.navigate([rutaPanel(sesion.rol)]);
    return false;
  }

  return true;
};

function rutaPanel(rol: string): string {
  switch (rol) {
    case 'administrador':
      return '/admin/panel';
    case 'tutor':
      return '/tutor/panel';
    default:
      return '/estudiante/panel';
  }
}