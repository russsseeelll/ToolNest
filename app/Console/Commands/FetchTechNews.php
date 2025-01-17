<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Fetch and store 20 random tech news articles';

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
                'pageSize' => 20,
            ]);

            if (!$response->successful()) {
                $this->error('Failed to fetch news. Response: ' . $response->body());
                return 1;
            }

            $articles = $response->json('articles') ?? [];

            // Clear the news table
            $this->info('Truncating the news table...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            News::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('Inserting articles into the database...');
            foreach ($articles as $article) {
                News::create([
                    'title' => $article['title'] ?? 'No Title',
                    'description' => $article['description'] ?? '',
                    'url' => $article['url'],
                    'source_name' => $article['source']['name'] ?? 'Unknown',
                    // Convert ISO 8601 datetime to MySQL-compatible format
                    'published_at' => isset($article['publishedAt'])
                        ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                        : now(),
                ]);
            }

            $this->info('Successfully fetched and stored 20 tech news articles.');
        } catch (\Exception $e) {
            $this->error('Error fetching tech news: ' . $e->getMessage());
            Log::error('FetchTechNews command failed.', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
