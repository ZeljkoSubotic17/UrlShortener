<?php

namespace App\Services;

use App\Models\Url;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class UrlShortenerService
{
    protected $client;
    protected $maxAttempts = 100;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function shortenUrl($url)
    {
        if (!$this->checkUrlSafety($url)) {
            return null;
        }

        $existingUrl = Url::where('original_url', $url)->first();
        if ($existingUrl) {
            return url('/something/' . $existingUrl->short_hash);
        }

        $attempts = 0;
        do {
            $shortHash = Str::random(6);
            $attempts++;
            if ($attempts >= $this->maxAttempts) {
                return null;
            }
        } while (Url::where('short_hash', $shortHash)->exists());

        $urlRecord = Url::create(['original_url' => $url, 'short_hash' => $shortHash]);

        return url('/something/' . $urlRecord->short_hash);
    }

    protected function checkUrlSafety($url)
    {
        $response = $this->client->post('https://safebrowsing.googleapis.com/v4/threatMatches:find', [
            'json' => [
                'client' => [
                    'clientId' => config('services.google.client_id'),
                    'clientVersion' => config('services.google.client_version')
                ],
                'threatInfo' => [
                    'threatTypes' => ["MALWARE", "SOCIAL_ENGINEERING"],
                    'platformTypes' => ["ANY_PLATFORM"],
                    'threatEntryTypes' => ["URL"],
                    'threatEntries' => [
                        ['url' => $url]
                    ]
                ]
            ],
            'query' => ['key' => config('services.google.api_key')]
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return empty($body['matches']);
    }

    public function getOriginalUrl($hash)
    {
        $url = Url::where('short_hash', $hash)->first();

        return $url ? $url->original_url : null;
    }
}
