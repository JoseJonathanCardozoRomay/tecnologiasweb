import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, map } from 'rxjs';
import { API_BASE, RespuestaApi } from '../constants/api';

export interface MateriaTutor {
  id_materia: number;
  nombre_materia: string;
}

export interface BloqueDisponibilidad {
  id_disponibilidad?: number | null;
  dia_semana: string;
  hora_inicio: string;
  hora_fin: string;
}

export interface Tutor {
  id_tutor: number;
  id_usuario: number;
  especialidad: string | null;
  biografia: string | null;
  nombre: string;
  apellido: string;
  correo: string;
  estado: string;
  materias: MateriaTutor[];
  disponibilidad?: BloqueDisponibilidad[];
}

export interface RespuestaDisponibilidad {
  id_tutor: number;
  bloques_guardados: number;
}

export interface Materia {
  id_materia: number;
  nombre_materia: string;
  id_carrera: number | null;
  nombre_carrera: string | null;
}

export interface NuevoTutor {
  nombre: string;
  apellido: string;
  correo: string;
  usuario: string;
  clave: string;
  especialidad?: string;
  biografia?: string;
  ids_materias?: number[];
}

export interface RespuestaAsignacion {
  id_tutor: number;
  materias_asignadas: number;
}

@Injectable({ providedIn: 'root' })
export class TutorService {
  private readonly http = inject(HttpClient);
  private static readonly URL = `${API_BASE}/TutorController.php`;

  getTutores(): Observable<Tutor[]> {
    return this.http
      .get<RespuestaApi<Tutor[]>>(TutorService.URL, { params: { action: 'listar' } })
      .pipe(map((respuesta) => respuesta.data ?? []));
  }

  getDetalleTutor(id: number): Observable<Tutor> {
    const params = new HttpParams().set('action', 'detalle').set('id', id);

    return this.http
      .get<RespuestaApi<Tutor>>(TutorService.URL, { params })
      .pipe(map((respuesta) => respuesta.data!));
  }

  getMiPerfil(): Observable<Tutor> {
    return this.http
      .get<RespuestaApi<Tutor>>(TutorService.URL, { params: { action: 'mi_perfil' } })
      .pipe(map((respuesta) => respuesta.data!));
  }

  getMaterias(): Observable<Materia[]> {
    return this.http
      .get<RespuestaApi<Materia[]>>(TutorService.URL, { params: { action: 'materias' } })
      .pipe(map((respuesta) => respuesta.data ?? []));
  }

  crearTutor(data: NuevoTutor): Observable<{ id_tutor: number; id_usuario: number }> {
    const params = new HttpParams().set('action', 'crear');

    return this.http
      .post<RespuestaApi<{ id_tutor: number; id_usuario: number }>>(TutorService.URL, data, { params })
      .pipe(map((respuesta) => respuesta.data!));
  }

  asignarMaterias(idTutor: number, idsMaterias: number[]): Observable<RespuestaAsignacion> {
    const params = new HttpParams().set('action', 'asignar_materias');

    return this.http
      .post<RespuestaApi<RespuestaAsignacion>>(
        TutorService.URL,
        { id_tutor: idTutor, ids_materias: idsMaterias },
        { params },
      )
      .pipe(map((respuesta) => respuesta.data!));
  }

  updateDisponibilidad(bloques: BloqueDisponibilidad[]): Observable<RespuestaDisponibilidad> {
    const params = new HttpParams().set('action', 'disponibilidad');

    return this.http
      .post<RespuestaApi<RespuestaDisponibilidad>>(TutorService.URL, { bloques }, { params })
      .pipe(map((respuesta) => respuesta.data!));
  }
}