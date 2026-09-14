<?php
namespace App\Services\Literature;
class LiteratureSearchService
{
    public function __construct(private QueryExpansionService $expand,private CrossrefService $crossref,private OpenAlexService $openalex,private ArticleDeduplicationService $dedupe,private RelevanceRankingService $ranker,private EvidenceExtractionService $evidence,private SintaRegistryService $sinta,private LiteratureSynthesisService $synthesis,private CitationFormatterService $citations){}
    public function run(array $input): array
    {
        $queries=$this->expand->expand($input['query']); $all=[];$errors=[];
        foreach(array_slice($queries,0,3) as $q){ foreach($input['sources'] as $src){try{$items=$src==='crossref'?$this->crossref->search($q,$input):$this->openalex->search($q,$input);$all=array_merge($all,$items);}catch(\Throwable $e){$errors[]=$src.': '.$e->getMessage();}}}
        $all=$this->dedupe->deduplicate($all); $all=$this->sinta->enrich($all); if(!empty($input['sinta_level']))$all=$this->sinta->filter($all,(int)$input['sinta_level']); if(!empty($input['only_abstract']))$all=array_values(array_filter($all,fn($a)=>!empty($a['abstract'])));
        $all=$this->ranker->rank($input['query'],$all); $selected=array_slice($all,0,(int)$input['limit']); $selected=$this->evidence->extract($input['query'],$selected);
        $syn=$this->synthesis->synthesize($input['query'],$selected,$input); $refs=array_map(fn($a)=>$this->citations->apa($a),$selected);
        return compact('queries','all','selected','syn','refs','errors');
    }
}
