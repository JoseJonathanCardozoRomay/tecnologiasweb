import { HttpErrorResponse } from '@angular/common/http';

export function httpErrorMessage(err: unknown, fallback = 'Ocurrió un error inesperado.'): string {
  if (err instanceof HttpErrorResponse) {
    const cuerpo = err.error as { message?: string } | null;
    return cuerpo?.message || fallback;
  }

  return fallback;
}