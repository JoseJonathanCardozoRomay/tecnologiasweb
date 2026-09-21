import { Component, inject, signal } from '@angular/core';
import { Tutoria, TutoriaService } from '../../core/services/tutoria.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

export const ESTADOS_TUTORIA = ['pendiente', 'confirmada', 'realizada', 'cancelada'] as const;

@Component({
  selector: 'app-reporte-tutorias',
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Reporte de Tutorías</h1>
        <p class="text-slate-500 text-sm mb-6">Resumen general de todas las tutorías del sistema.</p>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
          <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
              <select [value]="estado()" (change)="cambiarEstado($event)" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Todos</option>
                @for (est of estados; track est) {
                  <option [value]="est">{{ est }}</option>
                }
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Desde</label>
              <input type="date" [value]="fechaDesde()" (change)="cambiarDesde($event)" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Hasta</label>
              <input type="date" [value]="fechaHasta()" (change)="cambiarHasta($event)" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
            </div>
            <div class="flex items-end gap-2">
              <button type="button" (click)="aplicarFiltros()" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg py-2 transition">Filtrar</button>
              <button type="button" (click)="limpiarFiltros()" class="px-3 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">Limpiar</button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
          <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
            <p class="text-2xl font-bold text-slate-800">{{ tutorias().length }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Total</p>
          </div>
          @for (est of estados; track est) {
            <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
              <p class="text-2xl font-bold text-slate-800">{{ cantidad(est) }}</p>
              <p class="text-xs text-slate-500 mt-0.5 capitalize">{{ est }}</p>
            </div>
          }
        </div>

        @if (cargando()) {
          <p class="text-slate-400">Cargando tutorías...</p>
        } @else if (error()) {
          <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-4 py-3">{{ error() }}</div>
        } @else if (tutorias().length === 0) {
          <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-400">Sin resultados para el filtro seleccionado.</div>
        } @else {
          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-slate-200 text-left text-slate-500">
                  <th class="px-5 py-3 font-medium">Materia</th>
                  <th class="px-5 py-3 font-medium">Estudiante</th>
                  <th class="px-5 py-3 font-medium">Tutor</th>
                  <th class="px-5 py-3 font-medium">Fecha</th>
                  <th class="px-5 py-3 font-medium">Modalidad</th>
                  <th class="px-5 py-3 font-medium">Estado</th>
                </tr>
              </thead>
              <tbody>
                @for (tutoria of tutorias(); track tutoria.id_tutoria) {
                  <tr class="border-b border-slate-100 last:border-0">
                    <td class="px-5 py-3.5 text-slate-800">{{ tutoria.nombre_materia }}</td>
                    <td class="px-5 py-3.5 text-slate-700">{{ tutoria.estudiante_nombre }} {{ tutoria.estudiante_apellido }}</td>
                    <td class="px-5 py-3.5 text-slate-700">{{ tutoria.tutor_nombre }} {{ tutoria.tutor_apellido }}</td>
                    <td class="px-5 py-3.5 text-slate-600">{{ tutoria.fecha.slice(0, 10) }} {{ tutoria.hora_inicio.slice(11, 16) }}</td>
                    <td class="px-5 py-3.5 text-slate-600 capitalize">{{ tutoria.modalidad }}</td>
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
export class ReporteTutoriasComponent {
  private readonly tutoriaService = inject(TutoriaService);

  protected readonly estados = [...ESTADOS_TUTORIA];

  protected readonly tutorias = signal<Tutoria[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');

  protected readonly estado = signal('');
  protected readonly fechaDesde = signal('');
  protected readonly fechaHasta = signal('');

  protected readonly coloresEstado: Record<string, string> = {
    pendiente: 'bg-amber-100 text-amber-700',
    confirmada: 'bg-sky-100 text-sky-700',
    realizada: 'bg-emerald-100 text-emerald-700',
    cancelada: 'bg-rose-100 text-rose-700',
  };

  constructor() {
    this.cargar();
  }

  protected cantidad(estado: string): number {
    return this.tutorias().filter((t) => t.estado === estado).length;
  }

  protected claseEstado(estado: string): string {
    return this.coloresEstado[estado] ?? 'bg-slate-100 text-slate-600';
  }

  protected cambiarEstado(evento: Event): void {
    this.estado.set((evento.target as HTMLSelectElement).value);
  }

  protected cambiarDesde(evento: Event): void {
    this.fechaDesde.set((evento.target as HTMLInputElement).value);
  }

  protected cambiarHasta(evento: Event): void {
    this.fechaHasta.set((evento.target as HTMLInputElement).value);
  }

  protected aplicarFiltros(): void {
    this.cargar();
  }

  protected limpiarFiltros(): void {
    this.estado.set('');
    this.fechaDesde.set('');
    this.fechaHasta.set('');
    this.cargar();
  }

  private cargar(): void {
    this.tutoriaService
      .getTodasTutorias({
        estado: this.estado() || undefined,
        fecha_desde: this.fechaDesde() || undefined,
        fecha_hasta: this.fechaHasta() || undefined,
      })
      .subscribe({
        next: (data) => {
          this.tutorias.set(data);
          this.cargando.set(false);
        },
        error: (err: unknown) => {
          this.error.set(httpErrorMessage(err, 'No se pudieron cargar las tutorías.'));
          this.cargando.set(false);
        },
      });
  }
}