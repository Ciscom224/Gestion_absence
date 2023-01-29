<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;
    protected $table='filieres';
    protected $primarykey='filiere_id';

    public function filiere_module(){
        return $this->belongsTo('App\Models\Module','module_id');
    }

    public function filiere_professeur(){
        return $this->belongsTo('App\Models\Professeur','id');
    }

}
