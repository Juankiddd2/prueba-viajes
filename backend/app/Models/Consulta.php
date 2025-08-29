<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;
    protected $table = 'consultas';
    protected $fillable = [
        'ciudad_id', 'presupuesto_cop', 'presupuesto_convertido', 'tasa_cambio', 'clima', 'fecha_consulta'
    ];
    public $timestamps = false;

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }
}
