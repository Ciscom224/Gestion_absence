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
        Schema::create('professeurs', function (Blueprint $table) {
            $table->id('professeur_id');
            $table->foreign('professeur_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('cin', 8);
            $table->string('telephone', 13);
            $table->string('email')->unique();
            $table->string('photo');
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
        Schema::dropIfExists('professeurs');
    }
};
