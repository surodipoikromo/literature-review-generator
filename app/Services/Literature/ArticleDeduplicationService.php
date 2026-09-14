<?php
namespace App\Services\Literature;
use App\Support\Text;
class ArticleDeduplicationService
{
    public function deduplicate(array $articles): array
    {
        $seen=[];$out=[];
        foreach($articles as $a){
            $doi=mb_strtolower(trim((string)($a['doi']??''))); $title=Text::normalize($a['title']??'');
            $key=$doi?('doi:'.$doi):('title:'.$title);
            if(!$title || isset($seen[$key])) continue; $seen[$key]=true; $out[]=$a;
        }
        return $out;
    }
}
