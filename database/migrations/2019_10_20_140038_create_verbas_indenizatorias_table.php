<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerbasIndenizatoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verbas_indenizatorias', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('deputado_id');
            $table->string('descricao', 200);
            $table->string('emitente', 200);
            $table->date('data_referencia');
            $table->date('data_emissao');
            $table->float('valor_reembolso');
            $table->float('valor_despesa');
            $table->string('documento')->nullable();

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
        Schema::dropIfExists('verbas_indenizatorias');
    }
}
