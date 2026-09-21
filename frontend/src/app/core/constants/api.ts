export const API_BASE = 'http://localhost:8000/controllers';

export interface RespuestaApi<T> {
  status: 'success' | 'error';
  code: number;
  message: string;
  data?: T;
  details?: unknown;
  error?: unknown;
}