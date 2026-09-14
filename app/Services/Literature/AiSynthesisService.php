<?php
namespace App\Services\Literature;
use Illuminate\Support\Facades\Http;
class AiSynthesisService
{
    public function available(): bool { return (bool)config('literature.ai.enabled') && filled(config('literature.ai.api_key')); }
    public function synthesize(string $query, array $articles, string $language='id', string $mode='previous'): ?string
    {
        if(!$this->available()) return null;
        $evidence=collect($articles)->filter(fn($a)=>!empty($a['evidence']))->take(10)->map(function($a,$i){
            return ['id'=>'S'.($i+1),'title'=>$a['title'],'authors'=>$a['authors'],'year'=>$a['year'],'doi'=>$a['doi'],'evidence'=>collect($a['evidence'])->pluck('text')->all()];
        })->values()->all(); if(!$evidence)return null;
        $system='You synthesize academic literature using ONLY supplied evidence. Never invent authors, DOI, methods, results, statistics, or claims. Cite every substantive sentence using [S#]. If evidence is insufficient, say so explicitly.';
        $user="Topic: {$query}\nOutput language: {$language}\nMode: {$mode}\nEvidence JSON:\n".json_encode($evidence,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        $url=rtrim(config('literature.ai.base_url'),'/').'/chat/completions';
        $r=Http::withToken(config('literature.ai.api_key'))->timeout(45)->post($url,['model'=>config('literature.ai.model'),'messages'=>[['role'=>'system','content'=>$system],['role'=>'user','content'=>$user]],'temperature'=>0.2]);
        return $r->successful()?trim((string)$r->json('choices.0.message.content')):null;
    }
}
