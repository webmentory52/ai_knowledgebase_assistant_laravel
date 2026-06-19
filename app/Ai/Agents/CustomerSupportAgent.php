<?php

namespace App\Ai\Agents;

use Laravel\Ai\Concerns\HasConversations;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class CustomerSupportAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations, HasConversations;


    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return <<<PROMPT
                You are a customer support assistant.

Use ONLY the provided knowledge base context.

Rules:

- Answer using the supplied context.
- Be concise and accurate.
- If the answer is not present in the context, say:
  "I couldn't find that information in the knowledge base."
- Never invent policies, pricing, or procedures.
PROMPT;
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }
}
