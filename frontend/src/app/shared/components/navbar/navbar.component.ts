import { HttpClient } from '@angular/common/http';
import { Component, inject } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { API_BASE } from '../../../core/constants/api';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-navbar',
  imports: [RouterModule],
  template: `
    <header class="fixed top-0 inset-x-0 z-40 bg-white shadow-sm border-b border-slate-200">
      <div class="flex items-center justify-between px-4 md:px-6 h-16">
        <div class="flex items-center gap-3">
          <span class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-indigo-600 text-white font-bold">
            TW
          </span>
          <span class="font-semibold text-slate-800">Sistema de Tutorías</span>
        </div>

        <div class="flex items-center gap-3">
          <span class="hidden sm:block text-sm font-medium text-slate-700">
            {{ usuario?.nombre ?? 'Usuario' }}
          </span>

          <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize" [class]="claseBadge()">
            {{ usuario?.rol ?? 'Sin sesión' }}
          </span>

          <button
            type="button"
            (click)="cerrarSesion()"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
          >
            <span>Cerrar sesión</span>
          </button>
        </div>
      </div>
    </header>
  `,
})
export class Navbar {
  private readonly authService = inject(AuthService);
  private readonly http = inject(HttpClient);
  private readonly router = inject(Router);

  protected readonly usuario = this.authService.obtenerSesion();

  private readonly coloresBadge: Record<string, string> = {
    administrador: 'bg-rose-100 text-rose-700',
    tutor: 'bg-sky-100 text-sky-700',
    estudiante: 'bg-emerald-100 text-emerald-700',
  };

  protected claseBadge(): string {
    const rol = this.usuario?.rol ?? '';
    return this.coloresBadge[rol] ?? 'bg-slate-100 text-slate-700';
  }

  protected cerrarSesion(): void {
    this.http.get(`${API_BASE}/logout.php`, { responseType: 'text' }).subscribe({
      next: () => this.limpiarSesion(),
      error: () => this.limpiarSesion(),
    });
  }

  private limpiarSesion(): void {
    this.authService.cerrarSesion();
    this.router.navigate(['/login']);
  }
}