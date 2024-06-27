<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShortenUrlRequest;
use App\Services\UrlShortenerService;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// class UrlController extends Controller
// {
//     public function shorten(Request $request)
//     {
//         $request->validate(['url' => 'required|url']);

//         // Check if URL is safe
//         $isSafe = $this->checkUrlSafety($request->url);
//         if (!$isSafe) {
//             return response()->json(['error' => 'URL is not safe'], 400);
//         }

//         // Check for duplicate
//         $existingUrl = Url::where('original_url', $request->url)->first();
//         if ($existingUrl) {
//             return response()->json(['short_url' => url('/something/' . $existingUrl->short_hash)], 200);
//         }

//         // Generate unique short hash
//         $maxAttempts = 100; // Maksimalan broj pokušaja
//         $attempts = 0;
//         do {
//             $shortHash = Str::random(6);
//             $attempts++;
//             if ($attempts >= $maxAttempts) {
//                 return response()->json(['error' => 'Unable to generate unique hash, please try again later'], 500);
//             }
//         } while (Url::where('short_hash', $shortHash)->exists());

//         // Store in database
//         $url = Url::create(['original_url' => $request->url, 'short_hash' => $shortHash]);

//         return response()->json(['short_url' => url('/something/' . $url->short_hash)], 201);
//     }

//     protected function checkUrlSafety($url)
//     {
//         dd($url);
//         $client = new \GuzzleHttp\Client();
//         $response = $client->post('https://safebrowsing.googleapis.com/v4/threatMatches:find', [
//             'json' => [
//                 'client' => [
//                     'clientId' => 'yourcompanyname',
//                     'clientVersion' => '1.5.2'
//                 ],
//                 'threatInfo' => [
//                     'threatTypes' => ["MALWARE", "SOCIAL_ENGINEERING"],
//                     'platformTypes' => ["ANY_PLATFORM"],
//                     'threatEntryTypes' => ["URL"],
//                     'threatEntries' => [
//                         ['url' => $url]
//                     ]
//                 ]
//             ],
//             'query' => ['key' => env('GOOGLE_API_KEY')]
//         ]);
//         dd($response->getBody());
//         $body = json_decode((string) $response->getBody(), true);

//         return empty($body['matches']);
//     }

//     public function redirect($hash)
//     {
//         $url = Url::where('short_hash', $hash)->firstOrFail();
//         return redirect($url->original_url);
//     }
// }


class UrlController extends Controller
{
    protected $urlShortenerService;

    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function shorten(ShortenUrlRequest $request)
    {
        $shortUrl = $this->urlShortenerService->shortenUrl($request->url);

        if (!$shortUrl) {
            return response()->json(['error' => 'Unable to generate unique hash, please try again later'], 500);
        }

        return response()->json(['short_url' => $shortUrl], 201);
    }

    public function redirect($hash)
    {
        $originalUrl = $this->urlShortenerService->getOriginalUrl($hash);

        if (!$originalUrl) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return redirect($originalUrl);
    }
}
