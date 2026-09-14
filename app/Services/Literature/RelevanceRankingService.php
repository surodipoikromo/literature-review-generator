<?php
namespace App\Services\Literature;
use App\Support\Text;
class RelevanceRankingService
{
    public function rank(string $query, array $articles): array
    {
        $qt=Text::tokens($query); $qn=Text::normalize($query);
        foreach($articles as &$a){
            $title=Text::normalize($a['title']??''); $abs=Text::normalize($a['abstract']??'');
            $titleHits=count(array_intersect($qt,Text::tokens($title))); $absHits=count(array_intersect($qt,Text::tokens($abs)));
            $phrase=str_contains($title,$qn)||($qn && str_contains($abs,$qn));
            $score=($titleHits*12)+($absHits*3)+($phrase?20:0)+(!empty($a['doi'])?3:0)+(!empty($a['abstract'])?8:0)+(($a['open_access']??false)?2:0);
            $a['relevance_score']=min(100,round($score,1));
            $reasons=[]; if($titleHits)$reasons[]="$titleHits kata kunci cocok pada judul"; if($absHits)$reasons[]="$absHits kata kunci cocok pada abstrak"; if(!empty($a['abstract']))$reasons[]='memiliki abstrak'; if(!empty($a['doi']))$reasons[]='DOI tersedia'; $a['relevance_reason']=implode('; ',$reasons)?:'kecocokan metadata umum';
        }
        unset($a); usort($articles,fn($x,$y)=>($y['relevance_score']<=>$x['relevance_score'])); return $articles;
    }
}
