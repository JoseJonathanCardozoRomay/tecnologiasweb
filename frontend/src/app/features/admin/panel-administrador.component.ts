import { Component, inject } from '@angular/core';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-panel-administrador',
  imports: [RouterModule],
  template: `
    <div class="min-h-screen bg-slate-50 p-8">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-2xl font-bold text-slate-800">Panel de Administración</h1>
        <p class="text-slate-500 mt-2">Bienvenido(a), {{ nombre() }}. Administra los tutores y las tutorías del sistema.</p>
        <div class="mt-8 grid md:grid-cols-2 gap-4">
          <a routerLink="/admin/tutores" class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-md transition text-left">
            <h2 class="font-semibold text-slate-800">Gestión de Tutores</h2>
            <p class="text-sm text-slate-500 mt-1">Administra los perfiles y materias de los tutores.</p>
          </a>
          <a routerLink="/admin/tutorias" class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-md transition text-left">
            <h2 class="font-semibold text-slate-800">Todas las Tutorías</h2>
            <p class="text-sm text-slate-500 mt-1">Consulta el registro completo de tutorías.</p>
          </a>
        </div>
      </div>
    </div>
  `,
})
export class PanelAdministradorComponent {
  private readonly authService = inject(AuthService);

  protected nombre(): string {
    return this.authService.obtenerSesion()?.nombre ?? 'Administrador';
  }
}