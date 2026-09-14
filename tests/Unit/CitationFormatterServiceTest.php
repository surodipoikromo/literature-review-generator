<?php
namespace Tests\Unit;
use App\Services\Literature\CitationFormatterService;
use PHPUnit\Framework\TestCase;
class CitationFormatterServiceTest extends TestCase { public function test_formats_in_text():void{$s=new CitationFormatterService();$this->assertSame('(Putra & Sari, 2024)',$s->inText(['authors'=>['Andi Putra','Dina Sari'],'year'=>2024]));} }
