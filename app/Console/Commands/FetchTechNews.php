<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FetchTechNews extends Command
{
    protected $signature = 'fetch:tech-news';
    protected $description = 'Fetch and store the latest tech news articles';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        $this->info('Memory limit disabled.');
        $this->logInfo('Starting to fetch tech news...');
        $this->info('Current memory usage: ' . memory_get_usage(true) . ' bytes');

        $apiKey = '38b207b9cf2b49f4ac9f78b0951d9a28';
        $url = 'https://newsapi.org/v2/everything';
        $keywords = ['technology']; // Start with a single keyword for testing
        $domains = implode(',', [
            'techcrunch.com',
            'thenextweb.com',
            'wired.com',
            'engadget.com',
        ]);
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        try {
            $this->logInfo('Truncating the news table...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            News::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->logInfo('Fetching articles...');
            $articles = [];

            foreach ($keywords as $keyword) {
                $this->logInfo("Fetching news for keyword: $keyword...");

                $response = Http::timeout(10)->get($url, [
                    'apiKey' => $apiKey,
                    'q' => $keyword,
                    'domains' => $domains,
                    'from' => $yesterday,
                    'to' => $today,
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => 1,
                ]);

                if ($response->successful()) {
                    $this->logInfo("API response received successfully for keyword: $keyword.");
                    $fetchedArticles = $response->json('articles') ?? [];

                    foreach ($fetchedArticles as $article) {
                        $articles[] = [
                            'title' => $article['title'] ?? 'No Title',
                            'description' => $article['description'] ?? '',
                            'url' => $article['url'],
                            'source_name' => $article['source']['name'] ?? 'Unknown',
                            'published_at' => $article['publishedAt'],
                        ];
                    }

                    $this->logInfo('Articles fetched: ' . json_encode($articles, JSON_PRETTY_PRINT));
                } else {
                    $this->logError("Failed to fetch news for keyword: $keyword.");
                    $this->logError('Response body: ' . $response->body());
                }
            }

            if (!empty($articles)) {
                $this->logInfo('Saving articles to the database...');
                News::insert($articles);
                $this->logInfo('Articles saved successfully.');
            } else {
                $this->logWarning('No articles were fetched.');
            }

            $this->info('Successfully fetched and stored tech news articles.');
        } catch (\Throwable $e) {
            $this->logError('Error occurred: ' . $e->getMessage());
            $this->logError('Trace: ' . $e->getTraceAsString());
        }
    }

    private function logInfo(string $message)
    {
        $this->info($message);
        \Log::info($message);
    }

    private function logError(string $message)
    {
        $this->error($message);
        \Log::error($message);
    }

    private function logWarning(string $message)
    {
        $this->warn($message);
        \Log::warning($message);
    }
}
