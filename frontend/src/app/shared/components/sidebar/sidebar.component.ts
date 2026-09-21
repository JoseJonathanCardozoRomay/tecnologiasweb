import { Component, inject } from '@angular/core';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../core/services/auth.service';

interface EnlaceMenu {
  ruta: string;
  etiqueta: string;
}

@Component({
  selector: 'app-sidebar',
  imports: [RouterModule],
  template: `
    <aside class="w-64 shrink-0 min-h-full bg-slate-900 text-slate-100 hidden md:flex flex-col">
      <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-800">
        <span class="h-8 w-8 rounded-lg bg-indigo-600 text-white inline-flex items-center justify-center font-bold">
          TW
        </span>
        <span class="font-semibold">Menú</span>
      </div>

      <nav class="flex-1 p-3 space-y-1">
        @for (enlace of enlaces; track enlace.ruta) {
          <a
            [routerLink]="enlace.ruta"
            routerLinkActive="bg-slate-800 text-white"
            class="block px-4 py-2.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800 hover:text-white transition"
          >
            {{ enlace.etiqueta }}
          </a>
        }
      </nav>

      <div class="px-5 py-4 border-t border-slate-800 text-xs text-slate-500">
        <span class="capitalize">{{ rol ?? 'Sin sesión' }}</span>
      </div>
    </aside>
  `,
})
export class Sidebar {
  private readonly authService = inject(AuthService);

  protected readonly rol = this.authService.obtenerRol();
  protected readonly enlaces: EnlaceMenu[] = this.obtenerEnlaces(this.rol ?? '');

  private obtenerEnlaces(rol: string): EnlaceMenu[] {
    switch (rol) {
      case 'estudiante':
        return [
          { ruta: '/tutores', etiqueta: 'Buscar Tutores' },
          { ruta: '/mis-solicitudes', etiqueta: 'Mis Solicitudes' },
        ];
      case 'tutor':
        return [
          { ruta: '/disponibilidad', etiqueta: 'Mi Disponibilidad' },
          { ruta: '/solicitudes', etiqueta: 'Solicitudes Recibidas' },
        ];
      case 'administrador':
        return [
          { ruta: '/admin/tutores', etiqueta: 'Gestión de Tutores' },
          { ruta: '/admin/tutorias', etiqueta: 'Todas las Tutorías' },
        ];
      default:
        return [];
    }
  }
}