<?php

namespace Tests\Feature;

use App\Produtividade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_ok()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    public function test_dashboard_passes_required_variables_to_view()
    {
        $response = $this->get('/');

        $response->assertViewHasAll(['dados', 'linhas', 'linhaSelecionada', 'resumo']);
    }

    public function test_dashboard_shows_all_lines_when_no_filter()
    {
        factory(Produtividade::class)->create(['linha_produto' => 'Geladeira']);
        factory(Produtividade::class)->create(['linha_produto' => 'TV']);

        $response = $this->get('/');

        $dados = $response->viewData('dados');
        $this->assertCount(2, $dados);
        $this->assertNull($response->viewData('linhaSelecionada'));
    }

    public function test_dashboard_filters_by_linha()
    {
        factory(Produtividade::class)->create(['linha_produto' => 'Geladeira', 'quantidade_produzida' => 500, 'quantidade_defeitos' => 10]);
        factory(Produtividade::class)->create(['linha_produto' => 'TV', 'quantidade_produzida' => 300, 'quantidade_defeitos' => 5]);

        $response = $this->get('/?linha=TV');

        $dados = $response->viewData('dados');
        $this->assertCount(1, $dados);
        $this->assertEquals('TV', $dados->first()->linha_produto);
        $this->assertEquals('TV', $response->viewData('linhaSelecionada'));
    }

    public function test_dashboard_ignores_invalid_linha_filter()
    {
        factory(Produtividade::class)->create(['linha_produto' => 'Geladeira']);
        factory(Produtividade::class)->create(['linha_produto' => 'TV']);

        $response = $this->get('/?linha=LinhaInvalida');

        $response->assertStatus(200);
        $dados = $response->viewData('dados');
        $this->assertCount(2, $dados);
        $this->assertNull($response->viewData('linhaSelecionada'));
    }

    public function test_dashboard_calculates_efficiency_correctly()
    {
        factory(Produtividade::class)->create([
            'linha_produto'        => 'TV',
            'data_producao'        => '2026-01-01',
            'quantidade_produzida' => 1000,
            'quantidade_defeitos'  => 100,
        ]);

        $response = $this->get('/?linha=TV');

        $dados = $response->viewData('dados');
        $this->assertCount(1, $dados);
        $this->assertEquals(90.00, $dados->first()->eficiencia);
    }

    public function test_dashboard_aggregates_multiple_records_per_linha()
    {
        factory(Produtividade::class)->create([
            'linha_produto'        => 'Geladeira',
            'quantidade_produzida' => 600,
            'quantidade_defeitos'  => 0,
        ]);
        factory(Produtividade::class)->create([
            'linha_produto'        => 'Geladeira',
            'quantidade_produzida' => 400,
            'quantidade_defeitos'  => 0,
        ]);

        $response = $this->get('/?linha=Geladeira');

        $dados = $response->viewData('dados');
        $this->assertEquals(1000, $dados->first()->total_produzida);
        $this->assertEquals(100.00, $dados->first()->eficiencia);
    }

    public function test_resumo_always_reflects_global_totals_regardless_of_filter()
    {
        factory(Produtividade::class)->create(['linha_produto' => 'Geladeira', 'quantidade_produzida' => 800, 'quantidade_defeitos' => 0]);
        factory(Produtividade::class)->create(['linha_produto' => 'TV', 'quantidade_produzida' => 200, 'quantidade_defeitos' => 0]);

        $response = $this->get('/?linha=TV');

        $resumo = $response->viewData('resumo');
        $this->assertEquals(1000, $resumo->total_produzida);
    }

    public function test_dashboard_shows_all_four_linhas_in_filter_list()
    {
        $response = $this->get('/');

        $linhas = $response->viewData('linhas');
        $this->assertContains('Geladeira', $linhas);
        $this->assertContains('Máquina de Lavar', $linhas);
        $this->assertContains('TV', $linhas);
        $this->assertContains('Ar-Condicionado', $linhas);
    }

    public function test_dashboard_returns_empty_dados_when_no_records()
    {
        $response = $this->get('/');

        $dados = $response->viewData('dados');
        $this->assertCount(0, $dados);
    }
}
