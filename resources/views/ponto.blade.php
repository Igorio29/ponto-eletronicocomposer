<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponto</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }

        .top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            color: #38bdf8;
        }

        .user {
            font-size: 14px;
            color: #94a3b8;
        }

        .btn {
            padding: 12px 18px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .entrada {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            box-shadow: 0 0 10px rgba(34, 197, 94, 0.5);
        }

        .entrada:hover {
            transform: scale(1.05);
        }

        .saida {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
        }

        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .info strong {
            color: #38bdf8;
        }

        .info span {
            display: block;
            font-size: 13px;
            color: #94a3b8;
        }

        .empty {
            text-align: center;
            margin-top: 40px;
            color: #64748b;
        }

        hr {
            border: 1px solid #1e293b;
            margin: 25px 0;
        }

        .logout {
            background: linear-gradient(135deg, #475569, #1e293b);
            color: #fff;
            margin-left: 10px;
            box-shadow: 0 0 8px rgba(100, 116, 139, 0.5);
        }

        .logout:hover {
            transform: scale(1.05);
        }

        @media (max-width: 768px) {

            .container {
                margin: 20px 10px;
                padding: 15px;
            }

            .top {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .title {
                font-size: 22px;
            }

            .user {
                font-size: 13px;
            }

            .btn {
                width: 100%;
                padding: 14px;
                font-size: 14px;
            }

            .card {
                flex-direction:row;
                align-items: flex-start;
                gap: 10px;
            }

            .info strong {
                font-size: 16px;
            }

            .info span {
                font-size: 14px;
            }

            .saida,
            .logout,
            .entrada {
                width: 100%;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <div class="top">
            <div>
                <div class="title">⚡ TechClock</div>
                <div class="user">Logado como {{ auth()->user()->name }}</div>
            </div>

            <div style="display:flex; gap:10px;">
                <form method="POST" action="/entrada">
                    @csrf
                    <button class="btn entrada">+ Entrada</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn logout">Sair</button>
                </form>
            </div>
        </div>

        <hr>

        <h2>Registros</h2>

        @forelse($pontos as $ponto)
        <div class="card">
            <div class="info">
                <strong>{{ $ponto->user->name }}</strong>
                <span>Entrada: {{ $ponto->entrada }}</span>
                <span>Saída: {{ $ponto->saida ?? '---' }}</span>
            </div>

            @if(!$ponto->saida)
            <a href="/saida/{{ $ponto->id }}">
                <button class="btn saida">Finalizar</button>
            </a>
            @else
            @if(auth()->user()->nivel_acesso == 2)
            <form method="POST" action="/ponto/{{ $ponto->id }}" style="margin-left:10px;">
                @csrf
                @method('DELETE')
                <button class="btn saida">🗑 Deletar</button>
            </form>
            @endif
            @endif
        </div>
        @empty
        <div class="empty">Nenhum registro ainda.</div>
        @endforelse

    </div>

</body>

</html>