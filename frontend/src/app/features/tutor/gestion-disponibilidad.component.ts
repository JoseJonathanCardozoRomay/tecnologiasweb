import { Component, inject, signal } from '@angular/core';
import { BloqueDisponibilidad, TutorService } from '../../core/services/tutor.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

const DIAS_SEMANA = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];

interface DiaDisponibilidad {
  dia: string;
  activo: boolean;
  inicio: string;
  fin: string;
  id_disponibilidad: number | null;
}

@Component({
  selector: 'app-gestion-disponibilidad',
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Mi Disponibilidad</h1>
        <p class="text-slate-500 text-sm mb-6">Define los días y horarios en los que puedes dar tutorías.</p>

        @if (cargando()) {
          <p class="text-slate-400">Cargando tu disponibilidad...</p>
        } @else {
          @if (mensaje()) {
            <div [class]="exito() ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700'"
                 class="border rounded-lg px-4 py-2 text-sm mb-4">
              {{ mensaje() }}
            </div>
          }

          <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
              <h2 class="font-semibold text-slate-800">Horario semanal</h2>
              <span class="text-xs text-slate-400">Los días activos aparecen en la agenda</span>
            </div>

            <div class="divide-y divide-slate-100">
              @for (dia of dias(); track dia.dia) {
                <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center gap-3">
                  <label class="flex items-center gap-3 sm:w-44 shrink-0">
                    <input
                      type="checkbox"
                      [checked]="dia.activo"
                      (change)="marcarDia(dia, $event)"
                      class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <span class="text-sm font-medium text-slate-700 capitalize">{{ dia.dia }}</span>
                  </label>

                  <div class="flex items-center gap-2">
                    <input
                      type="time"
                      [value]="dia.inicio"
                      [disabled]="!dia.activo"
                      (input)="asignarHora(dia, 'inicio', $event)"
                      class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm disabled:opacity-40"
                    />
                    <span class="text-slate-400 text-sm">a</span>
                    <input
                      type="time"
                      [value]="dia.fin"
                      [disabled]="!dia.activo"
                      (input)="asignarHora(dia, 'fin', $event)"
                      class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm disabled:opacity-40"
                    />
                  </div>
                </div>
              }
            </div>

            <div class="px-5 py-4 bg-slate-50 border-t border-slate-200">
              <button
                type="button"
                (click)="guardar()"
                [disabled]="guardando()"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold rounded-lg px-6 py-2.5 transition"
              >
                {{ guardando() ? 'Guardando...' : 'Guardar disponibilidad' }}
              </button>
            </div>
          </div>
        }
      </div>
    </div>
  `,
})
export class GestionDisponibilidadComponent {
  private readonly tutorService = inject(TutorService);

  protected readonly dias = signal<DiaDisponibilidad[]>(
    DIAS_SEMANA.map((dia) => ({ dia, activo: false, inicio: '', fin: '', id_disponibilidad: null })),
  );
  protected readonly cargando = signal(true);
  protected readonly guardando = signal(false);
  protected readonly mensaje = signal('');
  protected readonly exito = signal(false);

  constructor() {
    this.cargar();
  }

  protected marcarDia(dia: DiaDisponibilidad, evento: Event): void {
    dia.activo = (evento.target as HTMLInputElement).checked;
    this.dias.set([...this.dias()]);
  }

  protected asignarHora(dia: DiaDisponibilidad, campo: 'inicio' | 'fin', evento: Event): void {
    const valor = (evento.target as HTMLInputElement).value;
    if (campo === 'inicio') {
      dia.inicio = valor;
    } else {
      dia.fin = valor;
    }
    this.dias.set([...this.dias()]);
  }

  protected guardar(): void {
    const bloques: BloqueDisponibilidad[] = [];

    for (const dia of this.dias()) {
      if (!dia.activo) {
        continue;
      }

      if (!dia.inicio || !dia.fin) {
        this.mensaje.set(`El día ${dia.dia} está activo pero le faltan horas.`);
        this.exito.set(false);
        return;
      }

      if (dia.inicio >= dia.fin) {
        this.mensaje.set(`El día ${dia.dia} tiene una hora de inicio posterior o igual al fin.`);
        this.exito.set(false);
        return;
      }

      bloques.push({
        id_disponibilidad: dia.id_disponibilidad ?? undefined,
        dia_semana: dia.dia,
        hora_inicio: dia.inicio,
        hora_fin: dia.fin,
      });
    }

    this.guardando.set(true);
    this.tutorService.updateDisponibilidad(bloques).subscribe({
      next: () => {
        this.guardando.set(false);
        this.exito.set(true);
        this.mensaje.set('Disponibilidad guardada correctamente.');
      },
      error: (err: unknown) => {
        this.guardando.set(false);
        this.exito.set(false);
        this.mensaje.set(httpErrorMessage(err));
      },
    });
  }

  private cargar(): void {
    this.tutorService.getMiPerfil().subscribe({
      next: (perfil) => {
        const actuales = this.dias();
        for (const bloque of perfil.disponibilidad ?? []) {
          const coincide = actuales.find((d) => d.dia === bloque.dia_semana);
          if (coincide) {
            coincide.activo = true;
            coincide.inicio = bloque.hora_inicio.slice(11, 16);
            coincide.fin = bloque.hora_fin.slice(11, 16);
            coincide.id_disponibilidad = bloque.id_disponibilidad ?? null;
          }
        }
        this.dias.set(actuales);
        this.cargando.set(false);
      },
      error: (err: unknown) => {
        this.mensaje.set(httpErrorMessage(err, 'No se pudo cargar tu disponibilidad.'));
        this.exito.set(false);
        this.cargando.set(false);
      },
    });
  }
}