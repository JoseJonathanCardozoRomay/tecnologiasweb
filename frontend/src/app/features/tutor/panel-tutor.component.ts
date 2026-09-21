import { Component, inject } from '@angular/core';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-panel-tutor',
  imports: [RouterModule],
  template: `
    <div class="min-h-screen bg-slate-50 p-8">
      <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-2xl font-bold text-slate-800">Panel del Tutor</h1>
        <p class="text-slate-500 mt-2">Bienvenido(a), {{ nombre() }}. Gestiona tu disponibilidad y tus tutorías.</p>
        <div class="mt-8 grid md:grid-cols-2 gap-4">
          <a routerLink="/solicitudes" class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-md transition text-left">
            <h2 class="font-semibold text-slate-800">Solicitudes Recibidas</h2>
            <p class="text-sm text-slate-500 mt-1">Confirma o cancela las tutorías solicitadas.</p>
          </a>
          <a routerLink="/disponibilidad" class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-md transition text-left">
            <h2 class="font-semibold text-slate-800">Mi Disponibilidad</h2>
            <p class="text-sm text-slate-500 mt-1">Administra tus bloques horarios semanales.</p>
          </a>
        </div>
      </div>
    </div>
  `,
})
export class PanelTutorComponent {
  private readonly authService = inject(AuthService);

  protected nombre(): string {
    return this.authService.obtenerSesion()?.nombre ?? 'Tutor';
  }
}