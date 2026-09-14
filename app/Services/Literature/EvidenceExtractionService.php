<?php
namespace App\Services\Literature;
use App\Support\Text;
class EvidenceExtractionService
{
    public function extract(string $query, array $articles): array
    {
        $qt=Text::tokens($query);
        foreach($articles as &$a){
            $abs=Text::clean($a['abstract']??''); $a['evidence']=[];
            if(!$abs) continue;
            $sentences=preg_split('/(?<=[.!?])\s+/u',$abs,-1,PREG_SPLIT_NO_EMPTY)?:[$abs];
            $scored=[]; foreach($sentences as $s){$hits=count(array_intersect($qt,Text::tokens($s))); if($hits>0)$scored[]=['text'=>$s,'score'=>$hits];}
            usort($scored,fn($x,$y)=>$y['score']<=>$x['score']);
            $a['evidence']=array_slice($scored,0,2); $a['evidence_level']='abstract';
        }
        unset($a); return $articles;
    }
}
