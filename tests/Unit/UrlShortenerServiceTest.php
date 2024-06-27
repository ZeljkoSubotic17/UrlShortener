<?php

namespace Tests\Unit;

use App\Services\UrlShortenerService;
use App\Models\Url;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use GuzzleHttp\Psr7\Response;

class UrlShortenerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_shorten_url_creates_new_url()
    {
        $responseBody = json_encode(['matches' => []]);
        $response = new Response(200, [], $responseBody);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('post')
            ->andReturn($response);

        $service = new UrlShortenerService($client);

        $originalUrl = 'https://example.com';
        $shortUrl = $service->shortenUrl($originalUrl);

        $this->assertNotNull($shortUrl);
        $this->assertDatabaseHas('urls', ['original_url' => $originalUrl]);
    }

    public function test_shorten_url_returns_existing_short_url()
    {
        $responseBody = json_encode(['matches' => []]);
        $response = new Response(200, [], $responseBody);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('post')
            ->andReturn($response);

        $existingUrl = Url::factory()->create(['original_url' => 'https://example.com']);

        $service = new UrlShortenerService($client);
        $shortUrl = $service->shortenUrl('https://example.com');

        $this->assertEquals(url('/something/' . $existingUrl->short_hash), $shortUrl);
    }

    public function test_shorten_url_fails_if_url_is_unsafe()
    {
        $responseBody = json_encode(['matches' => [['threatType' => 'MALWARE']]]);
        $response = new Response(200, [], $responseBody);

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('post')
            ->andReturn($response);

        $service = new UrlShortenerService($client);

        $originalUrl = 'https://malicious.com';
        $shortUrl = $service->shortenUrl($originalUrl);

        $this->assertNull($shortUrl);
        $this->assertDatabaseMissing('urls', ['original_url' => $originalUrl]);
    }
}
