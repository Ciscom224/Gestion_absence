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
        Schema::create('contact', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('professeur_id');
            $table->foreign('professeur_id')->references('professeur_id')->on('professeurs')->onDelete('cascade');

            $table->unsignedBigInteger('etudiant_id');
            $table->foreign('etudiant_id')->references('etudiant_id')->on('etudiants')->onDelete('cascade');

            $table->string('subject');
            $table->string('message');
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
        Schema::dropIfExists('contact');
    }
};
