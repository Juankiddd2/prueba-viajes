import { Routes } from '@angular/router';

import { SeleccionComponent } from './components/seleccion.component';
import { ResultadoComponent } from './components/resultado.component';

export const routes: Routes = [
	{ path: '', component: SeleccionComponent },
	{ path: 'resultado', component: ResultadoComponent },
	{ path: '**', redirectTo: '' }
];
