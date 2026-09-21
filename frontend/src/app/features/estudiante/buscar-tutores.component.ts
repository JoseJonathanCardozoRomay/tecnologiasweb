import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Tutor, TutorService } from '../../core/services/tutor.service';
import { TutoriaService } from '../../core/services/tutoria.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

@Component({
  selector: 'app-buscar-tutores',
  imports: [ReactiveFormsModule],
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Buscar Tutores</h1>
        <p class="text-slate-500 text-sm mb-6">Explora los tutores disponibles y agenda una sesión de reforzamiento.</p>

        @if (cargando()) {
          <p class="text-slate-400">Cargando tutores...</p>
        } @else if (error()) {
          <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-4 py-3">{{ error() }}</div>
        } @else if (tutores().length === 0) {
          <p class="text-slate-400">No hay tutores registrados.</p>
        } @else {
          <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @for (tutor of tutores(); track tutor.id_tutor) {
              <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <h2 class="font-semibold text-slate-800">{{ tutor.nombre }} {{ tutor.apellido }}</h2>
                    <p class="text-xs text-slate-500">{{ tutor.especialidad ?? 'Especialidad no definida' }}</p>
                  </div>
                  <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-bold">
                    {{ iniciales(tutor) }}
                  </span>
                </div>

                <div>
                  <p class="text-xs text-slate-400 mb-1.5">Materias que dicta</p>
                  <div class="flex flex-wrap gap-1.5">
                    @for (materia of tutor.materias; track materia.id_materia) {
                      <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-xs border border-sky-100">{{ materia.nombre_materia }}</span>
                    } @empty {
                      <span class="text-xs text-slate-400">Sin materias asignadas</span>
                    }
                  </div>
                </div>

                <button
                  type="button"
                  (click)="abrirSolicitud(tutor)"
                  [disabled]="tutor.materias.length === 0"
                  class="mt-auto bg-indigo-600 hover:bg-indigo-700 disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg py-2.5 transition"
                >
                  Agendar tutoría
                </button>
              </div>
            }
          </div>
        }
      </div>
    </div>

    @if (tutorSeleccionado(); as tutor) {
      <div class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" (click)="cerrarModal()">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4" (click)="$event.stopPropagation()">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Agendar con {{ tutor.nombre }} {{ tutor.apellido }}</h3>
            <button type="button" (click)="cerrarModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
          </div>

          @if (mensaje()) {
            <div [class]="exito() ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700'" class="border rounded-lg px-4 py-2 text-sm">
              {{ mensaje() }}
            </div>
          }

          <form [formGroup]="formulario" (ngSubmit)="enviarSolicitud()" class="space-y-3 pt-1">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Materia</label>
              <select formControlName="id_materia" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option [ngValue]="0" disabled>Selecciona una materia</option>
                @for (materia of tutor.materias; track materia.id_materia) {
                  <option [ngValue]="materia.id_materia">{{ materia.nombre_materia }}</option>
                }
              </select>
            </div>

            <div class="grid grid-cols-3 gap-3">
              <div class="col-span-3 md:col-span-1">
                <label class="block text-sm font-medium text-slate-700 mb-1">Fecha</label>
                <input type="date" formControlName="fecha" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Inicio</label>
                <input type="time" formControlName="hora_inicio" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Fin</label>
                <input type="time" formControlName="hora_fin" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Modalidad</label>
              <select formControlName="modalidad" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm">
                <option value="presencial">Presencial</option>
                <option value="virtual">Virtual</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones</label>
              <textarea formControlName="observaciones" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>

            <button
              type="submit"
              [disabled]="formulario.invalid || enviando()"
              class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold rounded-lg py-2.5 transition"
            >
              {{ enviando() ? 'Enviando...' : 'Enviar solicitud' }}
            </button>
          </form>
        </div>
      </div>
    }
  `,
})
export class BuscarTutoresComponent {
  private readonly tutorService = inject(TutorService);
  private readonly tutoriaService = inject(TutoriaService);
  private readonly fb = inject(FormBuilder);

  protected readonly tutores = signal<Tutor[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly tutorSeleccionado = signal<Tutor | null>(null);
  protected readonly mensaje = signal('');
  protected readonly exito = signal(false);
  protected readonly enviando = signal(false);

  protected readonly formulario = this.fb.nonNullable.group({
    id_materia: [0, Validators.required],
    fecha: ['', Validators.required],
    hora_inicio: ['', Validators.required],
    hora_fin: ['', Validators.required],
    modalidad: ['presencial', Validators.required],
    observaciones: [''],
  });

  constructor() {
    this.cargarTutores();
  }

  protected iniciales(tutor: Tutor): string {
    return `${tutor.nombre[0] ?? ''}${tutor.apellido[0] ?? ''}`.toUpperCase();
  }

  protected abrirSolicitud(tutor: Tutor): void {
    this.tutorSeleccionado.set(tutor);
    this.mensaje.set('');
    this.exito.set(false);
    this.formulario.reset({ modalidad: 'presencial' });

    if (tutor.materias.length === 1) {
      this.formulario.patchValue({ id_materia: tutor.materias[0].id_materia });
    }
  }

  protected cerrarModal(): void {
    this.tutorSeleccionado.set(null);
  }

  protected enviarSolicitud(): void {
    const tutor = this.tutorSeleccionado();
    if (!tutor || this.formulario.invalid) {
      return;
    }

    const valores = this.formulario.getRawValue();
    this.enviando.set(true);

    this.tutoriaService
      .solicitarTutoria({
        id_tutor: tutor.id_tutor,
        id_materia: valores.id_materia,
        fecha: valores.fecha,
        hora_inicio: valores.hora_inicio,
        hora_fin: valores.hora_fin,
        modalidad: valores.modalidad as 'presencial' | 'virtual',
        observaciones: valores.observaciones || undefined,
      })
      .subscribe({
        next: () => {
          this.enviando.set(false);
          this.exito.set(true);
          this.mensaje.set('Solicitud de tutoría enviada correctamente.');
          this.formulario.reset({ modalidad: 'presencial' });
        },
        error: (err: unknown) => {
          this.enviando.set(false);
          this.exito.set(false);
          this.mensaje.set(httpErrorMessage(err));
        },
      });
  }

  private cargarTutores(): void {
    this.tutorService.getTutores().subscribe({
      next: (data) => {
        this.tutores.set(data);
        this.cargando.set(false);
      },
      error: (err: unknown) => {
        this.error.set(httpErrorMessage(err, 'No se pudieron cargar los tutores.'));
        this.cargando.set(false);
      },
    });
  }
}