<?php
namespace App\Services\Literature;
class SintaRegistryService
{
    private array $registry=[];
    public function __construct(){ $path=storage_path('app/data/sinta_journals.json'); if(is_file($path)) $this->registry=json_decode(file_get_contents($path),true)?:[]; }
    public function enrich(array $articles): array
    {
        $idx=[]; foreach($this->registry as $j){ foreach(($j['issn']??[]) as $issn) $idx[$this->norm($issn)]=$j; }
        foreach($articles as &$a){ $match=null; foreach(($a['issn']??[]) as $issn){$k=$this->norm($issn); if(isset($idx[$k])){$match=$idx[$k];break;}} $a['sinta']=$match; }
        unset($a); return $articles;
    }
    public function filter(array $articles, ?int $level): array
    { if(!$level)return $articles; return array_values(array_filter($articles,fn($a)=>(int)($a['sinta']['level']??0)===$level)); }
    private function norm(string $s): string { return preg_replace('/[^0-9Xx]/','',$s)??$s; }
}
