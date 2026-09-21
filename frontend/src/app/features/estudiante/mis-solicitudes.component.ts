import { Component, inject, signal } from '@angular/core';
import { Tutoria, TutoriaService } from '../../core/services/tutoria.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

@Component({
  selector: 'app-mis-solicitudes',
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Mis Solicitudes</h1>
        <p class="text-slate-500 text-sm mb-6">Historial y estado de tus tutorías solicitadas.</p>

        @if (cargando()) {
          <p class="text-slate-400">Cargando solicitudes...</p>
        } @else if (error()) {
          <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-4 py-3">{{ error() }}</div>
        } @else if (tutorias().length === 0) {
          <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
            Aún no tienes tutorías solicitadas.
          </div>
        } @else {
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-slate-200 text-left text-slate-500">
                  <th class="px-5 py-3 font-medium">Materia</th>
                  <th class="px-5 py-3 font-medium">Tutor</th>
                  <th class="px-5 py-3 font-medium">Fecha</th>
                  <th class="px-5 py-3 font-medium">Estado</th>
                </tr>
              </thead>
              <tbody>
                @for (tutoria of tutorias(); track tutoria.id_tutoria) {
                  <tr class="border-b border-slate-100 last:border-0">
                    <td class="px-5 py-3.5 text-slate-800">{{ tutoria.nombre_materia }}</td>
                    <td class="px-5 py-3.5 text-slate-700">{{ tutoria.tutor_nombre }} {{ tutoria.tutor_apellido }}</td>
                    <td class="px-5 py-3.5 text-slate-600">{{ tutoria.fecha.slice(0, 10) }} {{ tutoria.hora_inicio.slice(11, 16) }}</td>
                    <td class="px-5 py-3.5">
                      <span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize" [class]="claseEstado(tutoria.estado)">
                        {{ tutoria.estado }}
                      </span>
                    </td>
                  </tr>
                }
              </tbody>
            </table>
          </div>
        }
      </div>
    </div>
  `,
})
export class MisSolicitudesComponent {
  private readonly tutoriaService = inject(TutoriaService);

  protected readonly tutorias = signal<Tutoria[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');

  protected readonly coloresEstado: Record<string, string> = {
    pendiente: 'bg-amber-100 text-amber-700',
    confirmada: 'bg-sky-100 text-sky-700',
    realizada: 'bg-emerald-100 text-emerald-700',
    cancelada: 'bg-rose-100 text-rose-700',
  };

  constructor() {
    this.cargar();
  }

  protected claseEstado(estado: string): string {
    return this.coloresEstado[estado] ?? 'bg-slate-100 text-slate-600';
  }

  private cargar(): void {
    this.tutoriaService.getMisTutorias().subscribe({
      next: (data) => {
        this.tutorias.set(data);
        this.cargando.set(false);
      },
      error: (err: unknown) => {
        this.error.set(httpErrorMessage(err, 'No se pudieron cargar tus solicitudes.'));
        this.cargando.set(false);
      },
    });
  }
}