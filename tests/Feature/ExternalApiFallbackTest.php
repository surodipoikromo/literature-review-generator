<?php
namespace Tests\Feature;
use App\Services\Literature\CrossrefService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
class ExternalApiFallbackTest extends TestCase
{
    public function test_crossref_failure_returns_empty_result(): void
    {
        Http::fake(['api.crossref.org/*' => Http::response([], 503)]);
        $this->assertSame([], app(CrossrefService::class)->search('system quality', ['limit'=>5]));
    }
}
