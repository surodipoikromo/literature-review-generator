<?php
namespace Tests\Unit;
use App\Services\Literature\QueryExpansionService;
use PHPUnit\Framework\TestCase;
class QueryExpansionServiceTest extends TestCase { public function test_expands_known_indonesian_terms():void{$r=(new QueryExpansionService())->expand('Pengaruh kualitas sistem terhadap kepuasan pengguna');$this->assertContains('system quality',$r);$this->assertContains('user satisfaction',$r);} }
