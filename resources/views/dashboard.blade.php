<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Eficiência — Planta A | LG Electronics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body    { font-family: 'Plus Jakarta Sans', sans-serif; }
        .f-mono { font-family: 'IBM Plex Mono', monospace; }
    </style>
</head>
<body class="bg-[#f4f5f7] min-h-screen text-gray-900 antialiased">

    {{-- HEADER --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <span class="text-[#a50034] font-bold text-lg tracking-tight flex-shrink-0">LG Electronics</span>
                <span class="hidden sm:block w-px h-5 bg-gray-200"></span>
                <span class="hidden sm:block text-sm text-gray-500 font-medium truncate">
                    Dashboard de Eficiência de Produção — Planta A
                </span>
            </div>
            <span class="flex-shrink-0 text-[11px] font-bold text-gray-500 bg-gray-100 border border-gray-200 rounded-full px-3 py-1 uppercase tracking-widest">
                Jan 2026
            </span>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-6">

        {{-- SUMMARY CARDS --}}
        @php
            $ef       = $resumo->eficiencia ?? 0;
            $efColor  = $ef >= 95 ? 'text-green-600'  : ($ef >= 85 ? 'text-amber-600'  : 'text-red-600');
            $efLabel  = $ef >= 95 ? 'Excelente'        : ($ef >= 85 ? 'Dentro da meta'  : 'Abaixo da meta');
        @endphp

        <div id="dashboard-summary" class="grid grid-cols-1 sm:grid-cols-3 gap-4 transition-opacity duration-150 ease-out">

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Produzido</span>
                </div>
                <p class="f-mono text-[2rem] font-medium text-gray-900 leading-none">
                    {{ number_format($resumo->total_produzida ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">unidades · jan/2026</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Defeitos</span>
                </div>
                <p class="f-mono text-[2rem] font-medium text-gray-900 leading-none">
                    {{ number_format($resumo->total_defeitos ?? 0, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-2">unidades com não-conformidade</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Eficiência Geral</span>
                </div>
                <p class="f-mono text-[2rem] font-medium leading-none {{ $efColor }}">
                    {{ number_format($ef, 2, ',', '.') }}%
                </p>
                <p class="text-xs text-gray-400 mt-2">{{ $efLabel }}</p>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <nav id="filter-bar" role="tablist" aria-label="Filtro por linha de produção" class="flex items-center gap-2 flex-wrap">
            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mr-1">Linha:</span>

            <a href="{{ route('dashboard') }}"
               role="tab"
               data-linha-pill
               data-linha-value=""
               data-active="{{ !$linhaSelecionada ? 'true' : 'false' }}"
               aria-selected="{{ !$linhaSelecionada ? 'true' : 'false' }}"
               class="px-3 py-1.5 rounded text-sm font-semibold border transition-colors duration-150
                      border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-gray-700
                      data-[active=true]:bg-[#a50034] data-[active=true]:text-white data-[active=true]:border-transparent
                      data-[active=true]:hover:bg-[#a50034] data-[active=true]:hover:text-white
                      focus:outline-none focus-visible:ring-2 focus-visible:ring-[#a50034] focus-visible:ring-offset-2">
                Todas
            </a>

            @foreach ($linhas as $linha)
                <a href="{{ route('dashboard', ['linha' => $linha]) }}"
                   role="tab"
                   data-linha-pill
                   data-linha-value="{{ $linha }}"
                   data-active="{{ $linhaSelecionada === $linha ? 'true' : 'false' }}"
                   aria-selected="{{ $linhaSelecionada === $linha ? 'true' : 'false' }}"
                   class="px-3 py-1.5 rounded text-sm font-semibold border transition-colors duration-150
                          border-gray-300 text-gray-500 hover:bg-gray-100 hover:text-gray-700
                          data-[active=true]:bg-[#a50034] data-[active=true]:text-white data-[active=true]:border-transparent
                          data-[active=true]:hover:bg-[#a50034] data-[active=true]:hover:text-white
                          focus:outline-none focus-visible:ring-2 focus-visible:ring-[#a50034] focus-visible:ring-offset-2">
                    {{ $linha }}
                </a>
            @endforeach
        </nav>

        {{-- TABLE --}}
        <div id="dashboard-table" class="bg-white border border-gray-200 rounded-lg overflow-hidden transition-opacity duration-150 ease-out">

            <div class="px-6 py-3 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Eficiência por Linha de Produção
                    @if ($linhaSelecionada)
                        <span class="text-gray-300 mx-1">·</span>
                        <span class="text-gray-600 normal-case tracking-normal">{{ $linhaSelecionada }}</span>
                    @endif
                </h2>
                <span class="text-[11px] text-gray-400 f-mono">{{ $dados->count() }} {{ $dados->count() === 1 ? 'linha' : 'linhas' }}</span>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Linha de Produto</th>
                        <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Qtd. Produzida</th>
                        <th class="px-6 py-3 text-right text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Qtd. Defeitos</th>
                        <th class="px-6 py-3 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Eficiência</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dados as $i => $item)
                        @php
                            $leftBorders = [
                                'Geladeira'        => 'border-l-blue-400',
                                'Máquina de Lavar' => 'border-l-cyan-500',
                                'TV'               => 'border-l-violet-500',
                                'Ar-Condicionado'  => 'border-l-emerald-500',
                            ];
                            $lb = $leftBorders[$item->linha_produto] ?? 'border-l-gray-300';

                            $ief      = $item->eficiencia;
                            $iefColor = $ief >= 95 ? 'text-green-600'  : ($ief >= 85 ? 'text-amber-600'  : 'text-red-600');
                            $iefBar   = $ief >= 95 ? 'bg-green-500'    : ($ief >= 85 ? 'bg-amber-500'    : 'bg-red-500');
                            $stripe   = $i % 2 !== 0 ? 'bg-gray-50/60' : 'bg-white';
                        @endphp
                        <tr class="{{ $stripe }} border-b border-gray-100 hover:bg-gray-50 transition-colors duration-100">
                            <td class="px-6 py-4 border-l-4 {{ $lb }}">
                                <span class="text-sm font-semibold text-gray-800">{{ $item->linha_produto }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="f-mono text-sm text-gray-700">
                                    {{ number_format($item->total_produzida, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="f-mono text-sm text-gray-500">
                                    {{ number_format($item->total_defeitos, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="f-mono text-sm font-semibold {{ $iefColor }}">
                                    {{ number_format($ief, 2, ',', '.') }}%
                                </span>
                                <div class="mt-1.5 h-1 w-28 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $iefBar }}" style="width: {{ min($ief, 100) }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-14 text-center text-sm text-gray-400">
                                Nenhum dado encontrado para o filtro selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>

    <footer class="border-t border-gray-200 bg-gray-100 mt-8 py-4">
        <p class="text-center text-[11px] text-gray-400 tracking-wide">
            Período: Janeiro 2026 &nbsp;·&nbsp; Planta A &nbsp;·&nbsp; LG Electronics
        </p>
    </footer>

    <script>
        (function () {
            const pills = document.querySelectorAll('[data-linha-pill]');
            const targets = ['dashboard-summary', 'dashboard-table']
                .map(id => document.getElementById(id))
                .filter(Boolean);

            if (!pills.length || targets.length !== 2 || !window.fetch || !window.history || !window.history.pushState) {
                return;
            }

            let inflight = null;

            function setActive(value) {
                pills.forEach(p => {
                    const on = p.dataset.linhaValue === value;
                    p.dataset.active = on ? 'true' : 'false';
                    p.setAttribute('aria-selected', on ? 'true' : 'false');
                });
            }

            function fade(state) {
                targets.forEach(t => {
                    t.style.opacity = state === 'out' ? '0.35' : '1';
                });
            }

            async function navigate(url, value, { push = true } = {}) {
                if (inflight) inflight.abort();
                const ctrl = new AbortController();
                inflight = ctrl;

                setActive(value);
                fade('out');

                try {
                    const res = await fetch(url, {
                        signal: ctrl.signal,
                        headers: { 'Accept': 'text/html', 'X-Requested-With': 'fetch' },
                        credentials: 'same-origin',
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const html = await res.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    targets.forEach(target => {
                        const fresh = doc.getElementById(target.id);
                        if (fresh) target.innerHTML = fresh.innerHTML;
                    });

                    if (push) history.pushState({ linha: value }, '', url);
                } catch (err) {
                    if (err.name === 'AbortError') return;
                    window.location.href = url;
                    return;
                } finally {
                    if (inflight === ctrl) {
                        inflight = null;
                        fade('in');
                    }
                }
            }

            pills.forEach((pill, idx) => {
                pill.addEventListener('click', e => {
                    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                    e.preventDefault();
                    navigate(pill.href, pill.dataset.linhaValue);
                });

                pill.addEventListener('keydown', e => {
                    if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
                    e.preventDefault();
                    const dir = e.key === 'ArrowRight' ? 1 : -1;
                    const next = pills[(idx + dir + pills.length) % pills.length];
                    next.focus();
                });
            });

            window.addEventListener('popstate', () => {
                const value = new URL(window.location.href).searchParams.get('linha') || '';
                const match = Array.from(pills).find(p => p.dataset.linhaValue === value);
                navigate(match ? match.href : pills[0].href, match ? value : '', { push: false });
            });
        })();
    </script>

</body>
</html>
