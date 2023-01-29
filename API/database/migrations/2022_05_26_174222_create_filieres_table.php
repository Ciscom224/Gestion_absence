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
        Schema::create('filieres', function (Blueprint $table) {
            $table->bigIncrements('id');;
            $table->string('libelle');
            $table->string('niveau');

            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');

            $table->unsignedBigInteger('professeur_id');
            $table->foreign('professeur_id')->references('professeur_id')->on('professeurs')->onDelete('cascade');

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
        Schema::dropIfExists('filieres');
    }
};
