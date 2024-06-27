<?php

namespace Tests\Feature;

use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_shorten_endpoint_creates_short_url()
    {
        $response = $this->post('/shorten', ['url' => 'https://example.com']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('urls', ['original_url' => 'https://example.com']);
    }

    public function test_shorten_endpoint_returns_existing_short_url()
    {
        $existingUrl = Url::factory()->create(['original_url' => 'https://example.com']);

        $response = $this->post('/shorten', ['url' => 'https://example.com']);

        $response->assertStatus(201);
        $response->assertJson(['short_url' => url('/something/' . $existingUrl->short_hash)]);
    }

    public function test_redirect_endpoint_redirects_to_original_url()
    {
        $url = Url::factory()->create(['original_url' => 'https://example.com']);

        $response = $this->get('/something/' . $url->short_hash);

        $response->assertRedirect($url->original_url);
    }

    public function test_shorten_endpoint_validates_url()
    {
        $this->json('POST', '/shorten', [
            'url' => 'not-a-valid-url'
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The url field must be a valid URL.',
                'errors' => [
                    'url' => [
                        'The url field must be a valid URL.'
                    ]
                ]
            ]);
    }
}
