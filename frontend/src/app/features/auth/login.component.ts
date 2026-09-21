import { HttpErrorResponse } from '@angular/common/http';
import { Component, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';
import { httpErrorMessage } from '../../shared/utils/http-error';

@Component({
  selector: 'app-login',
  imports: [ReactiveFormsModule],
  template: `
    <div class="min-h-screen flex items-center justify-center px-4 bg-gradient-to-br from-indigo-700 via-indigo-600 to-sky-500">
      <div class="w-full max-w-md">
        <div class="text-center mb-8">
          <span class="inline-flex items-center justify-center h-14 w-14 rounded-2xl bg-white/20 border border-white/30 text-white text-xl font-bold">
            TW
          </span>
          <h1 class="mt-4 text-2xl font-bold text-white">Sistema de Tutorías</h1>
          <p class="text-indigo-100 text-sm">Inicia sesión para continuar</p>
        </div>

        <form [formGroup]="formulario" (ngSubmit)="iniciarSesion()" class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 space-y-4">
          @if (error()) {
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm rounded-lg px-4 py-2">
              {{ error() }}
            </div>
          }

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Usuario o correo</label>
            <input
              formControlName="usuario"
              type="text"
              autocomplete="username"
              placeholder="admin"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña</label>
            <input
              formControlName="contrasena"
              type="password"
              autocomplete="current-password"
              class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
          </div>

          <button
            type="submit"
            [disabled]="formulario.invalid || enviando()"
            class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-semibold rounded-lg py-2.5 transition"
          >
            {{ enviando() ? 'Ingresando...' : 'Iniciar sesión' }}
          </button>
        </form>
      </div>
    </div>
  `,
})
export class LoginComponent {
  private readonly fb = inject(FormBuilder);
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  protected readonly formulario = this.fb.nonNullable.group({
    usuario: ['', Validators.required],
    contrasena: ['', Validators.required],
  });

  protected readonly error = signal('');
  protected readonly enviando = signal(false);

  protected iniciarSesion(): void {
    if (this.formulario.invalid) {
      return;
    }

    const { usuario, contrasena } = this.formulario.getRawValue();
    this.enviando.set(true);
    this.error.set('');

    this.authService.iniciarSesion({ usuario, contrasena }).subscribe({
      next: (sesion) => this.redirigirPorRol(sesion.rol),
      error: (err: unknown) => {
        this.enviando.set(false);
        this.error.set(httpErrorMessage(err));
      },
    });
  }

  private redirigirPorRol(rol: string): void {
    switch (rol) {
      case 'administrador':
        this.router.navigate(['/admin/panel']);
        break;
      case 'tutor':
        this.router.navigate(['/tutor/panel']);
        break;
      default:
        this.router.navigate(['/estudiante/panel']);
    }
  }
}