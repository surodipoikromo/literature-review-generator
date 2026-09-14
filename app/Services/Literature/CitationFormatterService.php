<?php
namespace App\Services\Literature;
class CitationFormatterService
{
    public function inText(array $a): string
    {
        $names=array_values($a['authors']??[]); $year=$a['year']??'n.d.';
        if(!$names)return '(Anon., '.$year.')';
        $family=fn($n)=>trim((string)preg_replace('/^.*\s/u','',$n));
        if(count($names)===1)return '('.$family($names[0]).', '.$year.')';
        if(count($names)===2)return '('.$family($names[0]).' & '.$family($names[1]).', '.$year.')';
        return '('.$family($names[0]).' et al., '.$year.')';
    }
    public function apa(array $a): string
    {
        $authors=$a['authors']??[]; $authorText=$authors?implode(', ',$authors):'Anon.';
        $year=$a['year']??'n.d.'; $title=$a['title']??''; $journal=$a['journal']??'';
        $doi=$a['doi']??null; $tail=$doi?'https://doi.org/'.$doi:($a['url']??'');
        return trim("{$authorText} ({$year}). {$title}. {$journal}. {$tail}");
    }
}
