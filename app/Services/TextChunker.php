<?php

namespace App\Services;

class TextChunker
{
    public function chunk(string $text, int $chunkSize = 1000, int $overlap = 200): array
    {
        $chunks = [];

        $text = preg_replace('/\s+/', ' ', $text);

        $length = strlen($text);

        $offset = 0;

        while ($offset < $length) {
            $chunk = substr(
                $text,
                $offset,
                $chunkSize
            );

            $chunks[] = trim($chunk);

            $offset += ($chunkSize - $overlap);
        }

        return array_filter($chunks);
    }
}
