<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table='modules';
    protected $fillable =['libelle'];
    protected $primaryKey = 'module_id';

    public function filiere_professeur(){
        return $this->hasMany('App\Models\Professeur','id');
    }

}
