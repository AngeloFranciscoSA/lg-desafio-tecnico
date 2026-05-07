<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutividadesTable extends Migration
{
    public function up()
    {
        Schema::create('produtividades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('linha_produto', ['Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado']);
            $table->date('data_producao');
            $table->unsignedInteger('quantidade_produzida');
            $table->unsignedInteger('quantidade_defeitos');
            $table->timestamps();

            $table->index('linha_produto');
            $table->index('data_producao');
        });
    }

    public function down()
    {
        Schema::dropIfExists('produtividades');
    }
}
