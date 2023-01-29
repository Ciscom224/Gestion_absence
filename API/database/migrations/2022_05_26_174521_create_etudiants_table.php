<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id('etudiant_id');
            $table->foreign('etudiant_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('cin', 8);
            $table->string('telephone', 13);
            $table->string('email')->unique();
            $table->string('photo');
            $table->string('cne');
            $table->string('parcours');
            $table->string('sexe');
            $table->unsignedBigInteger('filiere_id');
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etudiants');
    }
};
