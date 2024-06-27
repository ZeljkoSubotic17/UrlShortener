<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShortenUrlRequest;
use App\Services\UrlShortenerService;

class UrlController extends Controller
{
    protected $urlShortenerService;

    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }

    public function index()
    {
        return view('welcome');
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
