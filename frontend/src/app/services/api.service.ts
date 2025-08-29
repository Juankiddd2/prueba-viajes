import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private baseUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  getCiudades(): Observable<any> {
    return this.http.get(`${this.baseUrl}/ciudades`);
  }

  postConsulta(ciudadId: string, presupuesto: number): Observable<any> {
    return this.http.post(`${this.baseUrl}/consulta`, {
      ciudad_id: ciudadId,
      presupuesto_cop: presupuesto
    });
  }
}
