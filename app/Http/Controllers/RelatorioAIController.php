<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroqService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller responsável pela interface de Relatórios Inteligentes.
 */
class RelatorioAIController extends Controller
{
    /**
     * Exibe a tela inicial de relatórios.
     */
    public function index()
    {
        return view('relatorios.index');
    }

    /**
     * Processa a requisição AJAX para gerar um novo relatório.
     */
    public function gerar(Request $request, GroqService $groq)
    {
        // 1. Validação de Segurança: Garante que a chave da API existe no servidor
        if (empty(env('GROQ_API_KEY')) && empty(config('services.groq.key'))) {
             return response()->json([
                'error' => 'Configuração ausente: A chave GROQ_API_KEY não foi encontrada no ambiente do servidor.'
            ], 500);
        }

        // 2. Validação do Input do usuário
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        $prompt = $request->input('prompt');
        Log::debug('Iniciando geração de relatório AI: ' . $prompt);

        try {
            // 3. Solicita à IA a geração do comando SQL baseado no texto do usuário
            $sql = $groq->generateSql($prompt);

            if (!$sql) {
                return response()->json([
                    'error' => 'Não foi possível entender a solicitação ou gerar uma consulta segura para este pedido.'
                ], 422);
            }

            // 4. Executa a query no banco de dados (A IA garante que é apenas SELECT)
            $results = DB::select($sql);

            if (empty($results)) {
                return response()->json([
                    'report' => "Nenhum dado encontrado no banco para o pedido: \"$prompt\""
                ]);
            }

            // 5. Envia os dados encontrados de volta para a IA formatar o texto do relatório final
            $report = $groq->formatReport($prompt, $results);

            return response()->json([
                'report' => $report,
                'sql' => config('app.debug') ? $sql : null // Exibe o SQL gerado apenas se o modo Debug estiver ligado
            ]);

        } catch (\Exception $e) {
            // Log do erro real para o desenvolvedor
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            
            // Retorno genérico e seguro para o usuário final
            return response()->json([
                'error' => 'Ocorreu um erro interno ao processar seu relatório. Tente ser mais específico no pedido.'
            ], 500);
        }
    }
}
