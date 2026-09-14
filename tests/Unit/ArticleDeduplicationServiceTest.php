<?php
namespace Tests\Unit;
use App\Services\Literature\ArticleDeduplicationService;
use PHPUnit\Framework\TestCase;
class ArticleDeduplicationServiceTest extends TestCase { public function test_prefers_unique_doi():void{$a=[['doi'=>'10.1/a','title'=>'A'],['doi'=>'10.1/a','title'=>'A copy'],['doi'=>null,'title'=>'Unique title']];$r=(new ArticleDeduplicationService())->deduplicate($a);$this->assertCount(2,$r);} }
