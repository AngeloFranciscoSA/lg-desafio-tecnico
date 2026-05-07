# Desafio LG — Dashboard de Produtividade

Aplicação Laravel 7 que exibe um dashboard de produtividade por linha de produto, com indicadores de quantidade produzida, defeitos e eficiência.

## Stack

- PHP 7.4
- Laravel 7.29
- MySQL 8.0
- Tailwind CSS (via CDN)
- Docker / Docker Compose

## Como rodar

### Via Docker (recomendado)

```bash
docker compose up -d --build
docker compose exec app php artisan db:seed
```

App em `http://localhost:8000`.

`key:generate` (se necessário) e `migrate` rodam automaticamente no entrypoint do container.
O seed é manual para evitar duplicação ao reiniciar o container.

Para subir do zero (resetar volume MySQL):

```bash
docker compose down -v
docker compose up -d --build
docker compose exec app php artisan db:seed
```

### Local (sem Docker)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Requer PHP 7.4+ e MySQL acessível conforme `.env`.

## Testes

```bash
docker compose exec app php artisan test
# ou
php artisan test
```

Cobertura inclui:
- Model `Produtividade` (fillable, casts)
- Endpoint do dashboard (status, view, dados agregados)
- Cálculo de eficiência (casos com defeito, sem defeito, totalização)
- Filtro por linha de produto

## Estrutura

| Caminho | Função |
|---|---|
| `app/Produtividade.php` | Model com constantes das linhas |
| `app/Http/Controllers/DashboardController.php` | Agregação SQL e render do dashboard |
| `database/migrations/...create_produtividades_table.php` | Schema |
| `database/seeds/ProdutividadeSeeder.php` | Dados de demonstração |
| `database/factories/ProdutividadeFactory.php` | Factory para testes |
| `resources/views/dashboard.blade.php` | View com cards, tabela e filtros |
| `routes/web.php` | Rota única `GET /` |

## Endpoint

| Método | Rota | Descrição |
|---|---|---|
| GET | `/` | Dashboard. Aceita `?linha=<nome>` para filtrar por linha de produto. |

Linhas válidas: `Geladeira`, `Máquina de Lavar`, `TV`, `Ar-Condicionado`.

## Cálculo de Eficiência — nota sobre interpretação

A spec descreve textualmente:

> Eficiência (%) (produzida/defeitos)

A fórmula literal apresenta problemas:

1. **Divisão por zero** quando `defeitos = 0` — o cenário ideal de produção quebraria o cálculo.
2. **Não retorna percentual** — `produzida/defeitos` é uma razão sem limite superior, não uma fração 0–100.
3. **Escala não-linear** — `produzida=1000/defeitos=10 → 100`; `defeitos=1 → 1000`. Inviabiliza comparação entre linhas e barra de progresso.
4. **Diverge do padrão de manufatura** — métricas reais (FPY, taxa de defeito, OEE) não usam essa razão.

Adotada a fórmula padrão de **First Pass Yield (FPY)**:

```
eficiência (%) = (produzida - defeitos) / produzida × 100
```

Vantagens:

- Faixa fixa **0–100%** (percentual real)
- `defeitos = 0` → **100%** (ideal)
- `defeitos = produzida` → **0%** (pior caso)
- Direção correta: mais defeito → menor eficiência
- Comparável entre linhas, plotável em barra de progresso
- Padrão reconhecido em manufatura

Implementação no SQL com `NULLIF` para evitar divisão por zero quando `produzida = 0`:

```sql
ROUND(
    (SUM(quantidade_produzida) - SUM(quantidade_defeitos)) * 1.0
    / NULLIF(SUM(quantidade_produzida), 0)
    * 100,
    2
) AS eficiencia
```

Thresholds visuais na view:

| Faixa | Cor |
|---|---|
| ≥ 95% | Verde |
| 85–95% | Amarelo |
| < 85% | Vermelho |

## Modelo de dados

Tabela `produtividades`:

| Coluna | Tipo |
|---|---|
| `id` | PK |
| `linha_produto` | enum(`Geladeira`, `Máquina de Lavar`, `TV`, `Ar-Condicionado`) |
| `data_producao` | date |
| `quantidade_produzida` | unsigned int |
| `quantidade_defeitos` | unsigned int |
| `created_at` / `updated_at` | timestamp |

### DDL

```sql
CREATE TABLE `produtividades` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `linha_produto` ENUM('Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado') NOT NULL,
    `data_producao` DATE NOT NULL,
    `quantidade_produzida` INT UNSIGNED NOT NULL,
    `quantidade_defeitos` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    INDEX `produtividades_linha_produto_index` (`linha_produto`),
    INDEX `produtividades_data_producao_index` (`data_producao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### INSERTs de exemplo

```sql
INSERT INTO `produtividades`
    (`linha_produto`, `data_producao`, `quantidade_produzida`, `quantidade_defeitos`, `created_at`, `updated_at`)
VALUES
    ('Geladeira',        '2026-01-01', 850, 12, NOW(), NOW()),
    ('Geladeira',        '2026-01-02', 920, 30, NOW(), NOW()),
    ('Máquina de Lavar', '2026-01-01', 700,  8, NOW(), NOW()),
    ('Máquina de Lavar', '2026-01-02', 680, 22, NOW(), NOW()),
    ('TV',               '2026-01-01', 500,  5, NOW(), NOW()),
    ('TV',               '2026-01-02', 540, 18, NOW(), NOW()),
    ('Ar-Condicionado',  '2026-01-01', 410,  0, NOW(), NOW()),
    ('Ar-Condicionado',  '2026-01-02', 460, 15, NOW(), NOW());
```
