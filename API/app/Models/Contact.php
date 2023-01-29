<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table='contact';
    protected $fillable=['id','professeur_id','user_id','subject','message'];
    protected $primaryKey='id';

    public function professeur() {
        return $this->belongsTo(Professeur::class,'professeur_id');
    }

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
}
