import { Head } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { useStream } from '@laravel/stream-react';
import ReactMarkdown from 'react-markdown';

type Message = {
    role: 'user' | 'assistant';
    content: string;
};

export default function Index() {
    const [message, setMessage] = useState('');
    const [messages, setMessages] = useState<Message[]>([]);
    const accumulatedRef = useRef('');
    const bottomRef = useRef<HTMLDivElement>(null);


    const stream = useStream('/chat', {
        onData: (data: string) => {
            const lines = data
                .split(/\n/)
                .map(line => line.replace(/^data:\s*/, '').trim())
                .filter(line => line && line !== '[DONE]');

            for (const line of lines) {
                try {
                    const parsed = JSON.parse(line);

                    if (parsed.type === 'text_delta' && parsed.delta) {
                        accumulatedRef.current += parsed.delta;

                        // Update the last assistant message live
                        setMessages(prev => {
                            const updated = [...prev];
                            const last = updated[updated.length - 1];

                            if (last?.role === 'assistant') {
                                updated[updated.length - 1] = {
                                    ...last,
                                    content: accumulatedRef.current,
                                };
                            }

                            return updated;
                        });
                    }
                } catch (e) {

                }
            }
        }
    });

    useEffect(() => {
        bottomRef.current?.scrollIntoView({
            behavior: 'smooth',
        });
    }, [messages]);

    const send = async () => {
        if (!message.trim() || stream.isStreaming) {
            return;
        }

        const question = message;
        accumulatedRef.current = '';

        setMessages(prev => [
            ...prev,
            {
                role: 'user',
                content: question,
            },
            {
                role: 'assistant',
                content: '',
            },
        ]);

        setMessage('');

        await stream.send({
            message: question,
        });
    };

    return (
        <div className="flex h-screen flex-col bg-white">
            <Head title="Customer support Assistant" />

            <header className="border-b px-6 py-4">
                <h1 className="text-xl font-semibold">
                    Customer Support Assistant
                </h1>
            </header>

            <div className="flex-1 overflow-y-auto p-6">
                <div className="mx-auto max-w-4xl space-y-6">
                    {messages.map((msg, index) => {
                        return (
                            <div
                                key={index} className={msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'}
                            >
                                <div
                                    className={msg.role === 'user'
                                            ? 'max-w-3xl rounded-2xl bg-blue-600 px-4 py-3 text-white'
                                            : 'max-w-3xl rounded-2xl bg-gray-100 px-4 py-3 text-gray-900'
                                    }
                                >
                                    <div>
                                        <ReactMarkdown>
                                            {msg.content}
                                        </ReactMarkdown>
                                    </div>

                                    {msg.role === 'assistant'
                                        && index === messages.length - 1
                                        && stream.isStreaming && (
                                            <span className="animate-pulse">
                                                ▋
                                            </span>
                                        )}
                                </div>
                            </div>
                        )
                       }
                    )}
                </div>
            </div>

            <footer className="border-t p-4">
                <div className="mx-auto flex max-w-4xl gap-3">
                    <textarea value={message} onChange={(e) => setMessage(e.target.value)}
                        placeholder="Ask a support question..."
                        className="flex-1 resize-none rounded-xl border p-3"
                        rows={2}
                        onKeyDown={(e) => {
                            if (
                                e.key === 'Enter' &&
                                !e.shiftKey
                            ) {
                                e.preventDefault();
                                send();
                            }
                        }}
                    />

                    <button onClick={send} disabled={stream.isStreaming} className="rounded-xl bg-black px-5 py-3 text-white disabled:opacity-50"
                    >
                        Send
                    </button>
                </div>
            </footer>
        </div>
    );
}
