<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
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
    box-shadow: 0 0 8px rgba(100,116,139,0.5);
}

.logout:hover {
    transform: scale(1.05);
}
    </style>

</head>

<body>

    <div class="container">

        <div class="top">
    <div>
        <div class="title">⚡ TechClock</div>
        <div class="user">Logado como <?php echo e(auth()->user()->name); ?></div>
    </div>

    <div style="display:flex; gap:10px;">
        <form method="POST" action="/entrada">
            <?php echo csrf_field(); ?>
            <button class="btn entrada">+ Entrada</button>
        </form>

        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="btn logout">Sair</button>
        </form>
    </div>
</div>

        <hr>

        <h2>Registros</h2>

        <?php $__empty_1 = true; $__currentLoopData = $pontos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ponto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card">
            <div class="info">
                <strong><?php echo e($ponto->user->name); ?></strong>
                <span>Entrada: <?php echo e($ponto->entrada); ?></span>
                <span>Saída: <?php echo e($ponto->saida ?? '---'); ?></span>
            </div>

            <?php if(!$ponto->saida): ?>
            <a href="/saida/<?php echo e($ponto->id); ?>">
                <button class="btn saida">Finalizar</button>
            </a>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="empty">Nenhum registro ainda.</div>
        <?php endif; ?>

    </div>

</body>

</html><?php /**PATH C:\Users\Igor\Documents\PHP\ponto-eletronicocomposer\resources\views/ponto.blade.php ENDPATH**/ ?>