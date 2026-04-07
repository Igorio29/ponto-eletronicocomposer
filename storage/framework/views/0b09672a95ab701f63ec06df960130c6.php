<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Ponto Eletrônico')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#0f172a] overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-6">
 <h3 class="text-xl font-semibold text-white mb-6">Meus Registros</h3>
                    <div class="flex gap-2">
                        <form method="POST" action="/entrada">
                            <?php echo csrf_field(); ?>
                            <button class="btn entrada">+ Registrar Entrada</button>
                        </form>
                    </div>
                    
                </div>

               

                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $pontos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ponto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card">
                        <div class="info">
                            <strong><?php echo e($ponto->user->name); ?></strong>
                            <span>Entrada: <?php echo e($ponto->entrada); ?></span>
                            <span>Saída: <?php echo e($ponto->saida ?? '---'); ?></span>
                        </div>

                        <div class="flex items-center">
                            <?php if(!$ponto->saida): ?>
                            <a href="/saida/<?php echo e($ponto->id); ?>">
                                <button class="btn saida">Finalizar Turno</button>
                            </a>
                            <?php else: ?>
                                <?php if(auth()->user()->nivel_acesso == 2): ?>
                                <form method="POST" action="/ponto/<?php echo e($ponto->id); ?>" class="ml-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="btn saida">🗑 Apagar</button>
                                </form>
                                <?php endif; ?>
                                <span class="text-green-500 text-sm font-bold ml-2">✓ Concluído</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-10 text-gray-500">
                        Nenhum registro de ponto encontrado.
                    </div>
                    <?php endif; ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\Igor\Documents\PHP\ponto-eletronicocomposer\resources\views/ponto.blade.php ENDPATH**/ ?>