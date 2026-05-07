<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Produtividade extends Model
{
    protected $fillable = [
        'linha_produto',
        'data_producao',
        'quantidade_produzida',
        'quantidade_defeitos',
    ];

    protected $casts = [
        'data_producao' => 'date',
    ];

    const LINHAS = [
        'Geladeira',
        'Máquina de Lavar',
        'TV',
        'Ar-Condicionado',
    ];
}
