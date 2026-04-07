<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável pela comunicação com a API do Groq (IA).
 * Realiza a conversão de linguagem natural para SQL e formatação de relatórios.
 */
class GroqService
{
    // Chave da API obtida do .env ou config
    protected string $apiKey;
    
    // URL base da API do Groq (padrão OpenAI)
    protected string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    
    // Modelo de IA utilizado (Llama 3.3 70b é excelente para raciocínio lógico e SQL)
    protected string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        // Prioriza a chave configurada em config/services.php, caso contrário busca no .env
        $this->apiKey = config('services.groq.key') ?? env('GROQ_API_KEY');
    }

    /**
     * Etapa 1: Transforma o pedido do usuário (texto) em uma query SQL SELECT válida.
     */
    public function generateSql(string $prompt): ?string
    {
        // Obtém a estrutura das tabelas para "ensinar" a IA
        $schema = $this->getSchema();
        
        // Define as regras de comportamento da IA para gerar SQL
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

        // Requisição HTTP para a API do Groq
        $response = Http::withoutVerifying() // Desabilita verificação SSL para evitar erros em ambiente local Windows
            ->withToken($this->apiKey)
            ->post($this->baseUrl, [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Gere uma query SQL para: " . $prompt]
                ],
                'temperature' => 0, // Temperatura 0 garante respostas mais determinísticas e precisas (menos criatividade, mais técnica)
            ]);

        if ($response->failed()) {
            Log::error('Erro API Groq (Geração SQL): ' . $response->body());
            return null;
        }

        // Limpa a resposta para garantir que temos apenas o SQL puro
        $sql = trim($response->json('choices.0.message.content'));
        $sql = str_ireplace(['```sql', '```'], '', $sql);
        $sql = trim($sql);

        // Validação de Segurança: Apenas comandos SELECT são permitidos
        if (!str_starts_with(strtoupper($sql), 'SELECT')) {
            Log::warning('IA tentou gerar um comando não permitido: ' . $sql);
            return null;
        }

        return $sql;
    }

    /**
     * Etapa 2: Recebe o resultado do banco (array) e transforma em um relatório formatado (Markdown).
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
                'temperature' => 0.5, // Temperatura 0.5 permite uma linguagem um pouco mais natural na escrita do relatório
            ]);

        if ($response->failed()) {
            Log::error('Erro API Groq (Formatação Relatório): ' . $response->body());
            return "Erro ao formatar o relatório. Dados brutos: " . json_encode($data);
        }

        return $response->json('choices.0.message.content');
    }

    /**
     * Define o "Conhecimento" da IA sobre a base de dados do sistema.
     */
    protected function getSchema(): string
    {
        return "- users: id, name, email, nivel_acesso (1=user, 2=admin, admin_master=2), email_verified_at, password, created_at, updated_at
        - pontos: id, user_id (FK users.id), entrada (timestamp), saida (timestamp), created_at, updated_at";
    }
}
