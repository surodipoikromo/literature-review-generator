<?php
namespace Tests\Unit;
use App\Services\Literature\RelevanceRankingService;
use PHPUnit\Framework\TestCase;
class RelevanceRankingServiceTest extends TestCase { public function test_relevant_title_ranks_first():void{$a=[['title'=>'Unrelated biology','abstract'=>'cells','doi'=>null],['title'=>'System quality and user satisfaction','abstract'=>'information system success','doi'=>'10.1/x']];$r=(new RelevanceRankingService())->rank('system quality user satisfaction',$a);$this->assertSame('System quality and user satisfaction',$r[0]['title']);} }
