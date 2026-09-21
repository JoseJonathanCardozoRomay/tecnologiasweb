import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, map, tap } from 'rxjs';
import { API_BASE, RespuestaApi } from '../constants/api';

export interface Credenciales {
  usuario: string;
  contrasena: string;
}

export interface UsuarioSesion {
  id_usuario: number;
  nombre: string;
  rol: string;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly http = inject(HttpClient);
  private static readonly CLAVE_SESION = 'tutorias_sesion';

  iniciarSesion(credenciales: Credenciales): Observable<UsuarioSesion> {
    const cuerpo = new HttpParams()
      .set('usuario', credenciales.usuario)
      .set('contrasena', credenciales.contrasena);

    return this.http
      .post<RespuestaApi<UsuarioSesion>>(`${API_BASE}/login_procesar.php`, cuerpo, {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          Accept: 'application/json',
        },
      })
      .pipe(
        map((respuesta) => respuesta.data!),
        tap((usuario) => this.guardarSesion(usuario)),
      );
  }

  cerrarSesion(): void {
    localStorage.removeItem(AuthService.CLAVE_SESION);
  }

  obtenerSesion(): UsuarioSesion | null {
    const crudo = localStorage.getItem(AuthService.CLAVE_SESION);
    return crudo ? (JSON.parse(crudo) as UsuarioSesion) : null;
  }

  obtenerRol(): string | null {
    return this.obtenerSesion()?.rol ?? null;
  }

  verificarRol(rolesPermitidos: string[]): boolean {
    const rol = this.obtenerRol();
    return rol !== null && rolesPermitidos.includes(rol);
  }

  private guardarSesion(usuario: UsuarioSesion): void {
    localStorage.setItem(AuthService.CLAVE_SESION, JSON.stringify(usuario));
  }
}