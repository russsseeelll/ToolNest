<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchTechNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tech-news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store the latest tech news articles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching tech news from the API...');

        $apiKey = '38b207b9cf2b49f4ac9f78b0951d9a28';
        $url = 'https://newsapi.org/v2/everything';
        $keywords = 'technology';
        $domains = 'techcrunch.com,thenextweb.com,wired.com';
        $from = now()->subDay()->toDateString();
        $to = now()->toDateString();

        try {
            $response = Http::timeout(10)->get($url, [
                'apiKey' => $apiKey,
                'q' => $keywords,
                'domains' => $domains,
                'from' => $from,
                'to' => $to,
                'language' => 'en',
                'sortBy' => 'publishedAt',
                'pageSize' => 5,
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch news. Response: ' . $response->body());
                return 1;
            }

            $articles = $response->json('articles') ?? [];

            foreach ($articles as $article) {
                $this->info("Title: " . ($article['title'] ?? 'No Title'));
                $this->info("Description: " . ($article['description'] ?? 'No Description'));
                $this->info("URL: " . $article['url']);
                $this->info("Source: " . ($article['source']['name'] ?? 'Unknown') . "\n");
            }

            $this->info('Successfully fetched tech news articles.');
        } catch (\Exception $e) {
            $this->error('Error fetching tech news: ' . $e->getMessage());
            Log::error('FetchTechNews command failed.', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
