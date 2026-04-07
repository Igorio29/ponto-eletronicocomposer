<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\GroqService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RelatorioAIController extends Controller
{
    public function index()
    {
        return view('relatorios.index');
    }

    public function gerar(Request $request, GroqService $groq)
    {
        Log::debug('Iniciando geração de relatório AI: ' . $request->input('prompt'));
        
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        $prompt = $request->input('prompt');

        try {
            // 1. Gera SQL
            $sql = $groq->generateSql($prompt);

            if (!$sql) {
                return response()->json([
                    'error' => 'Não foi possível entender a solicitação ou gerar uma consulta segura para este pedido.'
                ], 422);
            }

            // 2. Executa SQL (Somente SELECT garantido pelo GroqService)
            $results = DB::select($sql);

            if (empty($results)) {
                return response()->json([
                    'report' => "Nenhum dado encontrado para: \"$prompt\""
                ]);
            }

            // 3. Formata Relatório
            $report = $groq->formatReport($prompt, $results);

            return response()->json([
                'report' => $report,
                'sql' => config('app.debug') ? $sql : null // Opcional: mostrar SQL apenas em debug
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ocorreu um erro interno ao processar seu relatório. Tente ser mais específico no pedido.'
            ], 500);
        }
    }
}
