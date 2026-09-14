<?php
namespace App\Support;
final class Text
{
    public static function clean(?string $text): string
    {
        $text = html_entity_decode(strip_tags((string)$text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        return trim($text);
    }
    public static function normalize(string $text): string
    {
        $text = mb_strtolower(self::clean($text));
        $text = preg_replace('/[^\pL\pN\s]+/u', ' ', $text) ?? $text;
        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }
    public static function tokens(string $text): array
    {
        $stop = array_flip(['dan','atau','yang','dengan','terhadap','pada','dalam','untuk','dari','the','of','and','or','to','in','on','a','an','is','are','study','research','pengaruh','hubungan']);
        $tokens = preg_split('/\s+/u', self::normalize($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return array_values(array_unique(array_filter($tokens, fn($t) => mb_strlen($t) > 2 && !isset($stop[$t]))));
    }
}
