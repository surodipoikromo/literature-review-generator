<?php
namespace App\Services\Literature;
use App\Support\Text;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class CrossrefService
{
    public function search(string $query, array $filters=[]): array
    {
        $key='crossref:'.sha1(json_encode([$query,$filters]));
        return Cache::remember($key, now()->addMinutes(config('literature.cache_minutes')), function() use($query,$filters){
            $params=['query.bibliographic'=>$query,'rows'=>min(50,(int)($filters['limit']??20)),'select'=>'DOI,title,author,published,container-title,URL,abstract,ISSN,type'];
            if (!empty(config('literature.crossref_mailto'))) $params['mailto']=config('literature.crossref_mailto');
            $filter=[]; if(!empty($filters['year_from']))$filter[]='from-pub-date:'.$filters['year_from'].'-01-01'; if(!empty($filters['year_to']))$filter[]='until-pub-date:'.$filters['year_to'].'-12-31'; if($filter)$params['filter']=implode(',',$filter);
            $r=Http::acceptJson()->timeout(config('literature.http_timeout'))->retry(2,300)->get('https://api.crossref.org/works',$params);
            if(!$r->successful()) return [];
            return collect($r->json('message.items',[]))->map(function($x){
                $date=$x['published']['date-parts'][0][0]??null;
                $authors=collect($x['author']??[])->map(fn($a)=>trim(($a['given']??'').' '.($a['family']??'')))->filter()->values()->all();
                return ['source'=>'Crossref','external_id'=>$x['DOI']??null,'doi'=>$x['DOI']??null,'title'=>Text::clean($x['title'][0]??''),'authors'=>$authors,'year'=>$date,'journal'=>Text::clean($x['container-title'][0]??''),'url'=>$x['URL']??null,'abstract'=>Text::clean($x['abstract']??''),'issn'=>$x['ISSN']??[],'open_access'=>null,'evidence_level'=>empty($x['abstract'])?'metadata':'abstract'];
            })->filter(fn($x)=>$x['title'])->values()->all();
        });
    }
}
