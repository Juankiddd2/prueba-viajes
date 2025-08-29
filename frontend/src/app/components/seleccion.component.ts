import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-seleccion',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './seleccion.component.html',
  styleUrls: ['./seleccion.component.css']
})
export class SeleccionComponent {
  ciudades: any[] = [];
  ciudadId: number | '' = '';
  presupuesto: number | null = null;
  error: string = '';

  constructor(private api: ApiService, private router: Router) {
    this.api.getCiudades().subscribe({
      next: (data: any) => this.ciudades = data,
      error: () => this.error = 'Error cargando ciudades'
    });
  }

  continuar() {
    if (!this.ciudadId || !this.presupuesto) {
      this.error = 'Todos los campos son obligatorios';
      return;
    }
    this.router.navigate(['/resultado'], {
      queryParams: {
        ciudadId: this.ciudadId,
        presupuesto: this.presupuesto
      }
    });
  }
}
