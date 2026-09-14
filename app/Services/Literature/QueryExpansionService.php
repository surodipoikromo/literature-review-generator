<?php
namespace App\Services\Literature;
use App\Support\Text;
class QueryExpansionService
{
    private array $dictionary = [
        'kualitas sistem' => 'system quality', 'kualitas informasi' => 'information quality',
        'kepuasan pengguna' => 'user satisfaction', 'sistem informasi' => 'information system',
        'layanan publik' => 'public services', 'friksi digital' => 'digital friction',
        'penerimaan teknologi' => 'technology acceptance', 'kepercayaan' => 'trust',
    ];
    public function expand(string $query): array
    {
        $q = Text::clean($query); $normalized = Text::normalize($q);
        $parts = [$q];
        foreach ($this->dictionary as $id => $en) {
            if (str_contains($normalized, $id)) { $parts[] = $id; $parts[] = $en; }
        }
        $tokens = Text::tokens($q);
        if (count($tokens) >= 2) $parts[] = implode(' ', array_slice($tokens, 0, min(6, count($tokens))));
        return array_values(array_unique(array_filter($parts)));
    }
}
