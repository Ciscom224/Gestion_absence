<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Filiere;

class Etudiant extends Model
{
    use HasFactory;

    protected $table='etudiants';
    protected $fillable=['etudiant_id','nom','prenom','cin','telephone','email','photo','filiere_id'];
    protected $primaryKey='etudiant_id';

    public function etudiantfiliere(){
        return $this->belongsTo('App\Models\Filiere','filiere_id');
    }
}
