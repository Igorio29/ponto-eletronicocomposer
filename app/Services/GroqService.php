<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    protected string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key') ?? env('GROQ_API_KEY');
    }

    /**
     * Gera uma query SQL a partir do prompt do usuário.
     */
    public function generateSql(string $prompt): ?string
    {
        $schema = $this->getSchema();
        
        $systemPrompt = "Você é um assistente especialista em SQL para MySQL (Versão 8+).
        O banco de dados possui as seguintes tabelas e colunas:
        $schema
        
        REGRAS CRÍTICAS:
        - Use APENAS sintaxe MySQL. Não use funções de SQLite (como STRFTIME).
        - SEMPRE use funções de agregação (SUM, COUNT, etc.) para colunas que não estão no GROUP BY para evitar erros de 'ONLY_FULL_GROUP_BY'.
        - Exemplo: Para calcular horas totais, use SUM(TIMESTAMPDIFF(HOUR, entrada, saida)).
        - Retorne APENAS o código SQL da query SELECT.
        - Não explique nada.
        - Se o usuário pedir algo impossível, retorne uma string vazia.";

        $response = Http::withoutVerifying()
            ->withToken($this->apiKey)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Gere uma query SQL para: " . $prompt]
                ],
                'temperature' => 0,
            ]);

        if ($response->failed()) {
            Log::error('Groq API Error (SQL Generation): ' . $response->body());
            return null;
        }

        $sql = trim($response->json('choices.0.message.content'));
        
        // Limpeza básica caso a IA tenha incluído markdown
        $sql = str_ireplace(['```sql', '```'], '', $sql);
        $sql = trim($sql);

        // Segurança: Garantir que começa com SELECT
        if (!str_starts_with(strtoupper($sql), 'SELECT')) {
            return null;
        }

        return $sql;
    }

    /**
     * Formata os dados resultantes em um relatório amigável.
     */
    public function formatReport(string $prompt, array $data): string
    {
        $systemPrompt = "Você é um assistente especialista em análise de dados.
        O usuário solicitou o seguinte relatório: \"$prompt\"
        
        Aqui estão os dados brutos recuperados do banco de dados:
        " . json_encode($data) . "
        
        INSTRUÇÕES:
        - Crie um relatório formatado em Markdown.
        - Use títulos, tabelas e listas para organizar as informações.
        - Seja profissional e direto.
        - Responda em Português do Brasil.
        - Se não houver dados, informe educadamente.";

        $response = Http::withoutVerifying()
            ->withToken($this->apiKey)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Formate o relatório com base nos dados fornecidos."]
                ],
                'temperature' => 0.5,
            ]);

        if ($response->failed()) {
            Log::error('Groq API Error (Report Formatting): ' . $response->body());
            return "Erro ao formatar o relatório. Dados brutos: " . json_encode($data);
        }

        return $response->json('choices.0.message.content');
    }

    protected function getSchema(): string
    {
        return "- users: id, name, email, nivel_acesso (1=user, 2=admin), email_verified_at, password, created_at, updated_at
        - pontos: id, user_id (FK users.id), entrada (timestamp), saida (timestamp), created_at, updated_at";
    }
}
