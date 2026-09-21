import { Routes } from '@angular/router';
import { authGuard } from './core/guards/auth.guard';

import { LoginComponent } from './features/auth/login.component';
import { PanelEstudianteComponent } from './features/estudiante/panel-estudiante.component';
import { BuscarTutoresComponent } from './features/estudiante/buscar-tutores.component';
import { MisSolicitudesComponent } from './features/estudiante/mis-solicitudes.component';
import { PanelTutorComponent } from './features/tutor/panel-tutor.component';
import { SolicitudesRecibidasComponent } from './features/tutor/solicitudes-recibidas.component';
import { GestionDisponibilidadComponent } from './features/tutor/gestion-disponibilidad.component';
import { PanelAdministradorComponent } from './features/admin/panel-administrador.component';
import { GestionTutoresComponent } from './features/admin/gestion-tutores.component';
import { ReporteTutoriasComponent } from './features/admin/reporte-tutorias.component';

export const routes: Routes = [
  { path: '', redirectTo: 'estudiante/panel', pathMatch: 'full' },

  { path: 'login', component: LoginComponent },

  { path: 'estudiante/panel', component: PanelEstudianteComponent, canActivate: [authGuard], data: { roles: ['estudiante'] } },
  { path: 'tutores', component: BuscarTutoresComponent, canActivate: [authGuard], data: { roles: ['estudiante'] } },
  { path: 'mis-solicitudes', component: MisSolicitudesComponent, canActivate: [authGuard], data: { roles: ['estudiante'] } },

  { path: 'tutor/panel', component: PanelTutorComponent, canActivate: [authGuard], data: { roles: ['tutor'] } },
  { path: 'solicitudes', component: SolicitudesRecibidasComponent, canActivate: [authGuard], data: { roles: ['tutor'] } },
  { path: 'disponibilidad', component: GestionDisponibilidadComponent, canActivate: [authGuard], data: { roles: ['tutor'] } },

  { path: 'admin/panel', component: PanelAdministradorComponent, canActivate: [authGuard], data: { roles: ['administrador'] } },
  { path: 'admin/tutores', component: GestionTutoresComponent, canActivate: [authGuard], data: { roles: ['administrador'] } },
  { path: 'admin/tutorias', component: ReporteTutoriasComponent, canActivate: [authGuard], data: { roles: ['administrador'] } },

  { path: '**', redirectTo: 'estudiante/panel' },
];