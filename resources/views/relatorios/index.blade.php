<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Relatórios com IA') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <label for="prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Descreva o relatório que deseja gerar
                        </label>
                        <div class="mt-1 flex gap-2">
                            <textarea id="prompt" name="prompt" rows="3" 
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Ex: Quantas horas cada funcionário trabalhou no mês de Março?"></textarea>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button id="btn-gerar" onclick="gerarRelatorio()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <span id="btn-text">Gerar Relatório</span>
                                <svg id="btn-spinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div id="resultado-container" class="hidden mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium mb-4">Resultado do Relatório</h3>
                        <div id="resultado-content" class="prose dark:prose-invert max-w-none bg-gray-50 dark:bg-gray-900 p-6 rounded-lg shadow-inner">
                            <!-- O relatório será injetado aqui -->
                        </div>
                        
                        <div id="sql-debug" class="mt-4 text-xs text-gray-500 hidden">
                           <p class="font-bold mb-1">SQL Gerado (Debug):</p>
                           <code id="sql-content" class="block p-2 bg-gray-100 dark:bg-gray-800 rounded"></code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluindo biblioteca Marked para converter Markdown em HTML -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        async function gerarRelatorio() {
            const prompt = document.getElementById('prompt').value;
            const btn = document.getElementById('btn-gerar');
            const btnText = document.getElementById('btn-text');
            const btnSpinner = document.getElementById('btn-spinner');
            const container = document.getElementById('resultado-container');
            const content = document.getElementById('resultado-content');
            const sqlDebug = document.getElementById('sql-debug');
            const sqlContent = document.getElementById('sql-content');

            if (!prompt.trim()) {
                alert('Por favor, digite o que deseja consultar.');
                return;
            }

            // UI Loading state
            btn.disabled = true;
            btnText.innerText = 'Processando...';
            btnSpinner.classList.remove('hidden');
            container.classList.add('hidden');

            try {
                const response = await fetch('{{ route("relatorios.gerar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ prompt: prompt })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Ocorreu um erro ao gerar o relatório.');
                }

                // Render Markdown
                content.innerHTML = marked.parse(data.report);
                container.classList.remove('hidden');

                if (data.sql) {
                    sqlContent.innerText = data.sql;
                    sqlDebug.classList.remove('hidden');
                } else {
                    sqlDebug.classList.add('hidden');
                }

            } catch (error) {
                alert(error.message);
            } finally {
                btn.disabled = false;
                btnText.innerText = 'Gerar Relatório';
                btnSpinner.classList.add('hidden');
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            }
        }
    </script>

    <style>
        /* Estilização básica para tabelas geradas pelo markdown */
        #resultado-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        #resultado-content th, #resultado-content td {
            border: 1px solid #e2e8f0;
            padding: 0.75rem;
            text-align: left;
        }
        .dark #resultado-content th, .dark #resultado-content td {
            border-color: #374151;
        }
        #resultado-content th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
        }
        .dark #resultado-content th {
            background-color: #4338ca;
            color: white;
        }
    </style>
</x-app-layout>
