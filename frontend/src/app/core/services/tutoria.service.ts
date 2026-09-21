import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, map } from 'rxjs';
import { API_BASE, RespuestaApi } from '../constants/api';

export interface SolicitudTutoria {
  id_tutor: number;
  id_materia: number;
  fecha: string;
  hora_inicio: string;
  hora_fin: string;
  modalidad: 'presencial' | 'virtual';
  observaciones?: string;
}

export interface Tutoria {
  id_tutoria: number;
  id_estudiante: number;
  id_tutor: number;
  id_materia: number;
  fecha: string;
  hora_inicio: string;
  hora_fin: string;
  modalidad: string;
  lugar_o_enlace: string | null;
  estado: 'pendiente' | 'confirmada' | 'realizada' | 'cancelada';
  observaciones: string | null;
  fecha_solicitud: string;
  tutor_nombre?: string;
  tutor_apellido?: string;
  especialidad?: string | null;
  estudiante_nombre?: string;
  estudiante_apellido?: string;
  nombre_materia: string;
}

export type EstadoTutoria = 'confirmada' | 'realizada' | 'cancelada';

export interface FiltroTutorias {
  estado?: string;
  fecha_desde?: string;
  fecha_hasta?: string;
}

@Injectable({ providedIn: 'root' })
export class TutoriaService {
  private readonly http = inject(HttpClient);
  private static readonly URL = `${API_BASE}/TutoriaController.php`;

  solicitarTutoria(data: SolicitudTutoria): Observable<{ id_tutoria: number }> {
    const params = new HttpParams().set('action', 'solicitar');

    return this.http
      .post<RespuestaApi<{ id_tutoria: number }>>(TutoriaService.URL, data, { params })
      .pipe(map((respuesta) => respuesta.data!));
  }

  getMisTutorias(estado?: EstadoTutoria): Observable<Tutoria[]> {
    const params = new HttpParams().set('action', 'mis_tutorias');

    if (estado) {
      params.set('estado', estado);
    }

    return this.http
      .get<RespuestaApi<Tutoria[]>>(TutoriaService.URL, { params })
      .pipe(map((respuesta) => respuesta.data ?? []));
  }

  cambiarEstado(id: number, estado: EstadoTutoria): Observable<{ id_tutoria: number; estado: EstadoTutoria }> {
    const params = new HttpParams().set('action', 'cambiar_estado');

    return this.http
      .post<RespuestaApi<{ id_tutoria: number; estado: EstadoTutoria }>>(
        TutoriaService.URL,
        { id_tutoria: id, nuevo_estado: estado },
        { params },
      )
      .pipe(map((respuesta) => respuesta.data!));
  }

  getTodasTutorias(filtros: FiltroTutorias = {}): Observable<Tutoria[]> {
    let params = new HttpParams().set('action', 'todas');

    if (filtros.estado) {
      params = params.set('estado', filtros.estado);
    }
    if (filtros.fecha_desde) {
      params = params.set('fecha_desde', filtros.fecha_desde);
    }
    if (filtros.fecha_hasta) {
      params = params.set('fecha_hasta', filtros.fecha_hasta);
    }

    return this.http
      .get<RespuestaApi<Tutoria[]>>(TutoriaService.URL, { params })
      .pipe(map((respuesta) => respuesta.data ?? []));
  }
}