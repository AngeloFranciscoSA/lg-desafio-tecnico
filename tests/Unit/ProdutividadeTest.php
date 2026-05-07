<?php

namespace Tests\Unit;

use App\Produtividade;
use PHPUnit\Framework\TestCase;

class ProdutividadeTest extends TestCase
{
    public function test_linhas_constant_has_four_entries()
    {
        $this->assertCount(4, Produtividade::LINHAS);
    }

    public function test_linhas_constant_contains_expected_values()
    {
        $this->assertContains('Geladeira', Produtividade::LINHAS);
        $this->assertContains('Máquina de Lavar', Produtividade::LINHAS);
        $this->assertContains('TV', Produtividade::LINHAS);
        $this->assertContains('Ar-Condicionado', Produtividade::LINHAS);
    }

    public function test_model_fillable_includes_all_production_fields()
    {
        $model = new Produtividade();
        $fillable = $model->getFillable();

        $this->assertContains('linha_produto', $fillable);
        $this->assertContains('data_producao', $fillable);
        $this->assertContains('quantidade_produzida', $fillable);
        $this->assertContains('quantidade_defeitos', $fillable);
    }

    public function test_data_producao_is_cast_to_date()
    {
        $casts = (new Produtividade())->getCasts();

        $this->assertArrayHasKey('data_producao', $casts);
        $this->assertEquals('date', $casts['data_producao']);
    }
}
