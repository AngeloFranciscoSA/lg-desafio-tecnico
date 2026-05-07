<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Produtividade;
use Faker\Generator as Faker;

$factory->define(Produtividade::class, function (Faker $faker) {
    $produzida = $faker->numberBetween(200, 1000);
    $defeitos = $faker->numberBetween(0, (int) ($produzida * 0.15));

    return [
        'linha_produto' => $faker->randomElement(Produtividade::LINHAS),
        'data_producao' => $faker->dateTimeBetween('2026-01-01', '2026-01-31')->format('Y-m-d'),
        'quantidade_produzida' => $produzida,
        'quantidade_defeitos' => $defeitos,
    ];
});
