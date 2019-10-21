<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeSocialDeputadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rede_social_deputados', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->bigInteger('deputado_id');
            $table->bigInteger('rede_id');
            $table->string('link');
            $table->string('nome', 200);

            $table->foreign('deputado_id')->references('id')->on('deputados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rede_social_deputados');
    }
}
