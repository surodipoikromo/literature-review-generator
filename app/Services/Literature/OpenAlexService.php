<?php
namespace App\Services\Literature;
use App\Support\Text;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class OpenAlexService
{
    public function search(string $query, array $filters=[]): array
    {
        $key='openalex:'.sha1(json_encode([$query,$filters]));
        return Cache::remember($key, now()->addMinutes(config('literature.cache_minutes')), function() use($query,$filters){
            $f=[]; if(!empty($filters['year_from']))$f[]='from_publication_date:'.$filters['year_from'].'-01-01'; if(!empty($filters['year_to']))$f[]='to_publication_date:'.$filters['year_to'].'-12-31'; if(!empty($filters['open_access']))$f[]='open_access.is_oa:true';
            $params=['search'=>$query,'per_page'=>min(50,(int)($filters['limit']??20))]; if($f)$params['filter']=implode(',',$f); if(config('literature.openalex_mailto'))$params['mailto']=config('literature.openalex_mailto'); if(config('literature.openalex_api_key'))$params['api_key']=config('literature.openalex_api_key');
            $r=Http::acceptJson()->timeout(config('literature.http_timeout'))->retry(2,300)->get('https://api.openalex.org/works',$params); if(!$r->successful()) return [];
            return collect($r->json('results',[]))->map(function($x){
                $inv=$x['abstract_inverted_index']??null; $abstract=''; if(is_array($inv)){ $words=[]; foreach($inv as $w=>$positions){foreach($positions as $p)$words[$p]=$w;} ksort($words); $abstract=implode(' ',$words); }
                $authors=collect($x['authorships']??[])->pluck('author.display_name')->filter()->values()->all();
                $src=$x['primary_location']['source']??[];
                return ['source'=>'OpenAlex','external_id'=>$x['id']??null,'doi'=>isset($x['doi'])?preg_replace('#^https://doi.org/#','',$x['doi']):null,'title'=>Text::clean($x['display_name']??''),'authors'=>$authors,'year'=>$x['publication_year']??null,'journal'=>Text::clean($src['display_name']??''),'url'=>$x['primary_location']['landing_page_url']??($x['id']??null),'abstract'=>Text::clean($abstract),'issn'=>$src['issn']??[],'open_access'=>$x['open_access']['is_oa']??null,'evidence_level'=>$abstract?'abstract':'metadata'];
            })->filter(fn($x)=>$x['title'])->values()->all();
        });
    }
}
