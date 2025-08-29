import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../services/api.service';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
  selector: 'app-resultado',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './resultado.component.html',
  styleUrls: ['./resultado.component.css']
})
export class ResultadoComponent {
  resultado: any = null;
  error: string = '';

  constructor(private route: ActivatedRoute, private api: ApiService) {
    this.route.queryParams.subscribe(params => {
      const ciudadId = params['ciudadId'];
      const presupuesto = params['presupuesto'];
      if (ciudadId && presupuesto) {
        this.api.postConsulta(ciudadId, presupuesto).subscribe({
          next: (data: any) => this.resultado = data,
          error: () => this.error = 'Error obteniendo resultado'
        });
      } else {
        this.error = 'Faltan datos para la consulta';
      }
    });
  }
}
