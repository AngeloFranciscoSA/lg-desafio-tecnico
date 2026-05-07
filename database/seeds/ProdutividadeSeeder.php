<?php

use App\Produtividade;
use Illuminate\Database\Seeder;

class ProdutividadeSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        foreach (Produtividade::LINHAS as $linha) {
            for ($dia = 1; $dia <= 31; $dia++) {
                $produzida = $faker->numberBetween(200, 1000);
                $defeitos = $faker->numberBetween(0, (int) ($produzida * 0.15));

                Produtividade::create([
                    'linha_produto' => $linha,
                    'data_producao' => sprintf('2026-01-%02d', $dia),
                    'quantidade_produzida' => $produzida,
                    'quantidade_defeitos' => $defeitos,
                ]);
            }
        }
    }
}
