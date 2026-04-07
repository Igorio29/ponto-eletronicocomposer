<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ponto Eletrônico') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#0f172a] overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-6">
 <h3 class="text-xl font-semibold text-white mb-6">Meus Registros</h3>
                    <div class="flex gap-2">
                        <form method="POST" action="/entrada">
                            @csrf
                            <button class="btn entrada">+ Registrar Entrada</button>
                        </form>
                    </div>
                    
                </div>

               

                <div class="space-y-4">
                    @forelse($pontos as $ponto)
                    <div class="card">
                        <div class="info">
                            <strong>{{ $ponto->user->name }}</strong>
                            <span>Entrada: {{ $ponto->entrada }}</span>
                            <span>Saída: {{ $ponto->saida ?? '---' }}</span>
                        </div>

                        <div class="flex items-center">
                            @if(!$ponto->saida)
                            <a href="/saida/{{ $ponto->id }}">
                                <button class="btn saida">Finalizar Turno</button>
                            </a>
                            @else
                                @if(auth()->user()->nivel_acesso == 2)
                                <form method="POST" action="/ponto/{{ $ponto->id }}" class="ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn saida">🗑 Apagar</button>
                                </form>
                                @endif
                                <span class="text-green-500 text-sm font-bold ml-2">✓ Concluído</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-500">
                        Nenhum registro de ponto encontrado.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            items-center;
        }

        .entrada {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .saida {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .relatorio {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.1);
        }

        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #334155;
            transition: 0.2s;
        }

        .card:hover {
            border-color: #38bdf8;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .info strong {
            color: #38bdf8;
            font-size: 1.1rem;
            display: block;
            margin-bottom: 4px;
        }

        .info span {
            display: block;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        @media (max-width: 640px) {
            .card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
            .flex.gap-2 {
                flex-direction: column;
                width: 100%;
            }
            form, a {
                width: 100%;
            }
        }
    </style>
</x-app-layout>