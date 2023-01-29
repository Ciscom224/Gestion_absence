<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professeur extends Model
{
    use HasFactory;

    protected $table='professeurs';
    protected $fillable=['etudiant_id','nom','prenom','cin','telephone','email','photo','cne','parcours','sexe','id_filiere'];
    protected $primaryKey='id';

    public function images(){
        return $this->hasMany('App\Models\Filiere','professeur_id');
    }

}
