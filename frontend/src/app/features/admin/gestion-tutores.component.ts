import { Component, WritableSignal, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Materia, Tutor, TutorService } from '../../core/services/tutor.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

@Component({
  selector: 'app-gestion-tutores',
  imports: [ReactiveFormsModule],
  template: `
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
      <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-slate-800">Gestión de Tutores</h1>
            <p class="text-slate-500 text-sm">Registra tutores y administra sus materias.</p>
          </div>
          <button type="button" (click)="abrirNuevo()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg px-4 py-2.5 transition">
            + Nuevo tutor
          </button>
        </div>

        @if (mensaje()) {
          <div [class]="exito() ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700'"
               class="border rounded-lg px-4 py-2 text-sm mb-5">
            {{ mensaje() }}
          </div>
        }

        @if (cargando()) {
          <p class="text-slate-400">Cargando tutores...</p>
        } @else if (error()) {
          <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-4 py-3">{{ error() }}</div>
        } @else {
          <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-5">
            @for (tutor of tutores(); track tutor.id_tutor) {
              <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex flex-col gap-3">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <h2 class="font-semibold text-slate-800">{{ tutor.nombre }} {{ tutor.apellido }}</h2>
                    <p class="text-xs text-slate-500">{{ tutor.correo }}</p>
                  </div>
                  <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 font-bold">
                    {{ iniciales(tutor) }}
                  </span>
                </div>

                <p class="text-sm text-slate-600 line-clamp-2">{{ tutor.especialidad ?? 'Sin especialidad' }}</p>

                <div class="flex flex-wrap gap-1.5">
                  @for (materia of tutor.materias; track materia.id_materia) {
                    <span class="px-2 py-0.5 rounded-full bg-sky-50 text-sky-700 text-xs border border-sky-100">{{ materia.nombre_materia }}</span>
                  } @empty {
                    <span class="text-xs text-slate-400">Sin materias asignadas</span>
                  }
                </div>

                <button
                  type="button"
                  (click)="abrirAsignacion(tutor)"
                  class="mt-auto text-sm font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg py-2 transition"
                >
                  Asignar materias
                </button>
              </div>
            }
          </div>
        }
      </div>
    </div>

    @if (mostrandoNuevo()) {
      <div class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" (click)="cerrarNuevo()">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto" (click)="$event.stopPropagation()">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Registrar nuevo tutor</h3>
            <button type="button" (click)="cerrarNuevo()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
          </div>

          <form [formGroup]="formNuevo" (ngSubmit)="crearTutor()" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                <input formControlName="nombre" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Apellido</label>
                <input formControlName="apellido" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Correo</label>
              <input formControlName="correo" type="email" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Usuario</label>
                <input formControlName="usuario" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
                <input formControlName="clave" type="password" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Especialidad</label>
              <input formControlName="especialidad" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm" />
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Biografía</label>
              <textarea formControlName="biografia" rows="2" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>

            <div>
              <p class="text-sm font-medium text-slate-700 mb-1.5">Materias</p>
              <div class="grid grid-cols-2 gap-1.5 max-h-32 overflow-y-auto">
                @for (materia of materias(); track materia.id_materia) {
                  <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input
                      type="checkbox"
                      [checked]="materiasNuevas().includes(materia.id_materia)"
                      (change)="alternarMateria(materia.id_materia, materiasNuevas)"
                      class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    {{ materia.nombre_materia }}
                  </label>
                }
              </div>
            </div>

            <button
              type="submit"
              [disabled]="formNuevo.invalid || guardandoNuevo()"
              class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold rounded-lg py-2.5 transition"
            >
              {{ guardandoNuevo() ? 'Registrando...' : 'Registrar tutor' }}
            </button>
          </form>
        </div>
      </div>
    }

    @if (tutorAsignacion(); as tutor) {
      <div class="fixed inset-0 z-50 bg-slate-900/60 flex items-center justify-center p-4" (click)="cerrarAsignacion()">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4" (click)="$event.stopPropagation()">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Materias de {{ tutor.nombre }} {{ tutor.apellido }}</h3>
            <button type="button" (click)="cerrarAsignacion()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
          </div>

          <div class="grid grid-cols-2 gap-1.5">
            @for (materia of materias(); track materia.id_materia) {
              <label class="flex items-center gap-2 text-sm text-slate-600">
                <input
                  type="checkbox"
                  [checked]="materiasAsignar().includes(materia.id_materia)"
                  (change)="alternarMateria(materia.id_materia, materiasAsignar)"
                  class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                />
                {{ materia.nombre_materia }}
              </label>
            }
          </div>

          <button
            type="button"
            (click)="asignar(tutor)"
            [disabled]="guardandoAsignacion()"
            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold rounded-lg py-2.5 transition"
          >
            {{ guardandoAsignacion() ? 'Guardando...' : 'Guardar materias' }}
          </button>
        </div>
      </div>
    }
  `,
})
export class GestionTutoresComponent {
  private readonly tutorService = inject(TutorService);
  private readonly fb = inject(FormBuilder);

  protected readonly tutores = signal<Tutor[]>([]);
  protected readonly materias = signal<Materia[]>([]);
  protected readonly cargando = signal(true);
  protected readonly error = signal('');
  protected readonly mensaje = signal('');
  protected readonly exito = signal(false);

  protected readonly mostrandoNuevo = signal(false);
  protected readonly guardandoNuevo = signal(false);
  protected readonly tutorAsignacion = signal<Tutor | null>(null);
  protected readonly guardandoAsignacion = signal(false);

  protected readonly materiasNuevas = signal<number[]>([]);
  protected readonly materiasAsignar = signal<number[]>([]);

  protected readonly formNuevo = this.fb.nonNullable.group({
    nombre: ['', Validators.required],
    apellido: ['', Validators.required],
    correo: ['', [Validators.required, Validators.email]],
    usuario: ['', Validators.required],
    clave: ['', [Validators.required, Validators.minLength(6)]],
    especialidad: [''],
    biografia: [''],
  });

  constructor() {
    this.cargarTutores();
    this.cargarMaterias();
  }

  protected iniciales(tutor: Tutor): string {
    return `${tutor.nombre[0] ?? ''}${tutor.apellido[0] ?? ''}`.toUpperCase();
  }

  protected abrirNuevo(): void {
    this.mostrandoNuevo.set(true);
    this.mensaje.set('');
    this.formNuevo.reset({
      nombre: '', apellido: '', correo: '', usuario: '', clave: '', especialidad: '', biografia: '',
    });
    this.materiasNuevas.set([]);
  }

  protected cerrarNuevo(): void {
    this.mostrandoNuevo.set(false);
  }

  protected abrirAsignacion(tutor: Tutor): void {
    this.tutorAsignacion.set(tutor);
    this.materiasAsignar.set(tutor.materias.map((m) => m.id_materia));
    this.mensaje.set('');
  }

  protected cerrarAsignacion(): void {
    this.tutorAsignacion.set(null);
  }

  protected alternarMateria(id: number, seleccion: WritableSignal<number[]>): void {
    const actuales = seleccion();
    seleccion.set(actuales.includes(id) ? actuales.filter((x) => x !== id) : [...actuales, id]);
  }

  protected crearTutor(): void {
    if (this.formNuevo.invalid) {
      return;
    }

    this.guardandoNuevo.set(true);
    const datos = this.formNuevo.getRawValue();

    this.tutorService
      .crearTutor({
        nombre: datos.nombre,
        apellido: datos.apellido,
        correo: datos.correo,
        usuario: datos.usuario,
        clave: datos.clave,
        especialidad: datos.especialidad || undefined,
        biografia: datos.biografia || undefined,
        ids_materias: this.materiasNuevas(),
      })
      .subscribe({
        next: () => {
          this.guardandoNuevo.set(false);
          this.mostrandoNuevo.set(false);
          this.exito.set(true);
          this.mensaje.set('Tutor registrado correctamente.');
          this.cargarTutores();
        },
        error: (err: unknown) => {
          this.guardandoNuevo.set(false);
          this.exito.set(false);
          this.mensaje.set(httpErrorMessage(err));
        },
      });
  }

  protected asignar(tutor: Tutor): void {
    this.guardandoAsignacion.set(true);

    this.tutorService.asignarMaterias(tutor.id_tutor, this.materiasAsignar()).subscribe({
      next: () => {
        this.guardandoAsignacion.set(false);
        this.tutorAsignacion.set(null);
        this.exito.set(true);
        this.mensaje.set('Materias actualizadas correctamente.');
        this.cargarTutores();
      },
      error: (err: unknown) => {
        this.guardandoAsignacion.set(false);
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

  private cargarMaterias(): void {
    this.tutorService.getMaterias().subscribe({
      next: (data) => this.materias.set(data),
      error: () => undefined,
    });
  }
}