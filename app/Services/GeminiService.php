<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.gemini.base_url');
        $this->apiKey = config('services.gemini.key');
    }

    public function generateText($prompt)
    {
        $url = "{$this->baseUrl}models/gemini-pro:generateContent?key={$this->apiKey}";

        $response = Http::post($url, [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ]
        ]);

        return $response->json();
    }
}
