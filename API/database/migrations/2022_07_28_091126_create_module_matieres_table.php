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
        Schema::create('module_matieres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('libelle');
            $table->string('niveau');

            $table->unsignedBigInteger('module_id');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');

            $table->unsignedBigInteger('matiere_id');
            $table->foreign('matiere_id')->references('id')->on('matieres')->onDelete('cascade');

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
        Schema::dropIfExists('module_matieres');
    }
};
