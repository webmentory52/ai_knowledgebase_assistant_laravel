<?php

namespace App\Http\Controllers;

use App\Ai\Agents\CustomerSupportAgent;
use App\Services\KnowledgeBaseRagService;
use Illuminate\Http\Request;
use Laravel\Ai\Enums\Lab;

class ChatController extends Controller
{
    public function __construct(private readonly KnowledgeBaseRagService $rag)
    {
    }

    public function index()
    {
        return inertia('chat/Index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'message' => ['required', 'string'],
        ]);

        $question = $request->string('message');

        $documents = $this->rag->search($question);

        $context = $this->rag->buildContext($documents);

        $prompt = <<<PROMPT
                Knowledge Base Context:

                {$context}

                Customer Question:

                {$question}
        PROMPT;

        return (new CustomerSupportAgent())
            ->stream(
                prompt: $prompt,
                provider: Lab::OpenAI,
                timeout: 600
            );

    }
}
