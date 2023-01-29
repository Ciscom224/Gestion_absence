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
        Schema::create('absences', function (Blueprint $table) {
            $table->bigIncrements('absence_id');

            $table->unsignedBigInteger('professeur_id');
            $table->foreign('professeur_id')->references('professeur_id')->on('professeurs')->onDelete('cascade');

            $table->unsignedBigInteger('etudiant_id');
            $table->foreign('etudiant_id')->references('etudiant_id')->on('etudiants')->onDelete('cascade');

            $table->datetime('date_heure');
            $table->boolean('justifie')->default(0);
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
        Schema::dropIfExists('absences');
    }
};
