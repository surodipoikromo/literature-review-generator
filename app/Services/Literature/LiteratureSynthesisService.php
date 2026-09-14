<?php

namespace App\Services\Literature;

class LiteratureSynthesisService
{
    public function __construct(
        private CitationFormatterService $citations,
        private AiSynthesisService $ai
    ) {}

    public function synthesize(string $query, array $articles, array $options = []): array
    {
        $usable = array_values(
            array_filter($articles, fn ($a) => !empty($a['evidence']))
        );

        $useAi = ($options['use_ai'] ?? false) && $this->ai->available();

        if ($useAi) {
            $text = $this->ai->synthesize(
                $query,
                $usable,
                $options['output_language'] ?? 'id',
                $options['mode'] ?? 'previous'
            );

            if ($text) {
                return [
                    'text' => $text,
                    'engine' => 'ai-grounded',
                ];
            }
        }

        if (!$usable) {
            return [
                'text' => 'Belum ada artikel dengan abstrak/evidence yang cukup untuk menyusun klaim. Artikel metadata-only tetap ditampilkan, tetapi tidak digunakan untuk synthesis.',
                'engine' => 'fallback',
            ];
        }

        $lang = $options['output_language'] ?? 'id';
        $chunks = [];

        foreach (array_slice($usable, 0, 6) as $article) {
            $evidence = trim($article['evidence'][0]['text'] ?? '');

            if ($evidence === '') {
                continue;
            }

            $citation = $this->citations->inText($article);

            $chunks[] = "{$evidence} {$citation}";
        }

        if (empty($chunks)) {
            return [
                'text' => 'Belum ada evidence yang cukup untuk menyusun literature review.',
                'engine' => 'fallback',
            ];
        }

        return [
            'text' => implode("\n\n", $chunks),
            'engine' => 'template-grounded',
        ];
    }
}