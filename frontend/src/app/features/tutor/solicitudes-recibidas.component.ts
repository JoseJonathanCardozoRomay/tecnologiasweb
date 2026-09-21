import { Component, inject, signal } from '@angular/core';
import { EstadoTutoria, Tutoria, TutoriaService } from '../../core/services/tutoria.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

@Component({
  selector: 'app-solicitudes-recibidas',
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Solicitudes Recibidas</h1>
        <p class="text-slate-500 text-sm mb-6">Gestiona las tutorías que tus estudiantes han solicitado.</p>

        @if (cargando()) {
          <p class="text-slate-400">Cargando solicitudes...</p>
        } @else if (error()) {
          <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-4 py-3">{{ error() }}</div>
        } @else if (tutorias().length === 0) {
          <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">
            No tienes solicitudes de tutoría por ahora.
          </div>
        } @else {
          <div class="space-y-4">
            @for (tutoria of tutorias(); track tutoria.id_tutoria) {
              <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col lg:flex-row lg:items-center gap-4">
                <div class="flex-1 space-y-1.5">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="font-semibold text-slate-800">{{ tutoria.nombre_materia }}</h2>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full capitalize" [class]="claseEstado(tutoria.estado)">
                      {{ tutoria.estado }}
                    </span>
                  </div>
                  <p class="text-sm text-slate-600">Estudiante: {{ tutoria.estudiante_nombre }} {{ tutoria.estudiante_apellido }}</p>
                  <p class="text-sm text-slate-500">
                    {{ tutoria.fecha.slice(0, 10) }} · {{ tutoria.hora_inicio.slice(11, 16) }} - {{ tutoria.hora_fin.slice(11, 16) }} ·
                    {{ tutoria.modalidad }}
                  </p>
                  @if (tutoria.observaciones) {
                    <p class="text-xs text-slate-400 italic">"{{ tutoria.observaciones }}"</p>
                  }
                </div>

                <div class="flex gap-2 shrink-0">
                  @if (tutoria.estado === 'pendiente') {
                    <button type="button" (click)="cambiarEstado(tutoria, 'confirmada')" class="px-3 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 rounded-lg transition">
                      Confirmar
                    </button>
                    <button type="button" (click)="cambiarEstado(tutoria, 'cancelada')" class="px-3 py-2 text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                      Cancelar
                    </button>
                  }

                  @if (tutoria.estado === 'confirmada') {
                    <button type="button" (click)="cambiarEstado(tutoria, 'realizada')" class="px-3 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">
                      Marcar realizada
                    </button>
                    <button type="button" (click)="cambiarEstado(tutoria, 'cancelada')" class="px-3 py-2 text-sm font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-lg transition">
                      Cancelar
                    </button>
                  }
                </div>
              </div>
            }
          </div>
        }
      </div>
    </div>
  `,
})
export class SolicitudesRecibidasComponent {
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

  protected cambiarEstado(tutoria: Tutoria, estado: EstadoTutoria): void {
    this.tutoriaService.cambiarEstado(tutoria.id_tutoria, estado).subscribe({
      next: () => this.cargar(),
      error: (err: unknown) => this.error.set(httpErrorMessage(err)),
    });
  }

  private cargar(): void {
    this.tutoriaService.getMisTutorias().subscribe({
      next: (data) => {
        this.tutorias.set(data);
        this.cargando.set(false);
      },
      error: (err: unknown) => {
        this.error.set(httpErrorMessage(err, 'No se pudieron cargar las solicitudes.'));
        this.cargando.set(false);
      },
    });
  }
}