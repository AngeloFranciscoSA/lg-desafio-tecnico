<?php

namespace App\Http\Controllers;

use App\Produtividade;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $linhaParam = $request->query('linha');
        $linhaSelecionada = ($linhaParam && in_array($linhaParam, Produtividade::LINHAS, true))
            ? $linhaParam
            : null;

        $query = Produtividade::query()
            ->selectRaw('
                linha_produto,
                SUM(quantidade_produzida) as total_produzida,
                SUM(quantidade_defeitos) as total_defeitos,
                ROUND((SUM(quantidade_produzida) - SUM(quantidade_defeitos)) * 1.0 / NULLIF(SUM(quantidade_produzida), 0) * 100, 2) as eficiencia
            ')
            ->groupBy('linha_produto')
            ->orderBy('linha_produto');

        if ($linhaSelecionada) {
            $query->where('linha_produto', $linhaSelecionada);
        }

        $dados = $query->get();

        $resumo = Produtividade::query()
            ->selectRaw('
                SUM(quantidade_produzida) as total_produzida,
                SUM(quantidade_defeitos) as total_defeitos,
                ROUND((SUM(quantidade_produzida) - SUM(quantidade_defeitos)) * 1.0 / NULLIF(SUM(quantidade_produzida), 0) * 100, 2) as eficiencia
            ')
            ->first();

        return view('dashboard', [
            'dados' => $dados,
            'linhas' => Produtividade::LINHAS,
            'linhaSelecionada' => $linhaSelecionada,
            'resumo' => $resumo,
        ]);
    }
}
