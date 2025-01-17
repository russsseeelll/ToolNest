<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchTechNews extends Command
{
    protected $signature = 'fetch:tech-news';
    protected $description = 'Fetch and store the latest tech news articles';

    public function handle()
    {
        // Increase memory limit for the script
        ini_set('memory_limit', '512M');

        $this->info('Starting to fetch tech news...');
        Log::info('Tech news fetch started.');

        $apiKey = '38b207b9cf2b49f4ac9f78b0951d9a28';
        $url = 'https://newsapi.org/v2/everything';

        $keywords = [
            'artificial intelligence', 'machine learning', 'blockchain', 'quantum computing',
            'cybersecurity', 'cloud computing', 'programming languages', 'web development',
            'startup innovations', 'tech gadgets', 'infrastructure', 'data centers',
            '5G technology', 'networking', 'IoT', 'AR and VR', 'mobile technology',
            'semiconductors', 'edge computing', 'robotics', 'technology trends', 'developer tools'
        ];

        $domains = implode(',', [
            'techcrunch.com',
            'thenextweb.com',
            'wired.com',
            'engadget.com',
            'arstechnica.com',
            'theverge.com',
            'venturebeat.com',
            'gizmodo.com',
            'cnet.com',
            'zdnet.com',
            'techradar.com',
            'tomshardware.com',
            'digitaltrends.com',
            'pcmag.com',
            'slashdot.org'
        ]);

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $this->info('Truncating the news table...');
        Log::info('Truncating news table.');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        News::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $articles = [];
        shuffle($keywords);

        foreach ($keywords as $keyword) {
            if (count($articles) >= 20) {
                break;
            }

            $this->info("Fetching news for keyword: $keyword...");
            Log::info("Fetching news for keyword: $keyword");

            try {
                $response = Http::timeout(10)->get($url, [
                    'apiKey' => $apiKey,
                    'q' => $keyword,
                    'domains' => $domains,
                    'from' => $yesterday,
                    'to' => $today,
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => 5,
                ]);

                if (!$response->successful()) {
                    throw new \Exception("Failed to fetch news: " . $response->body());
                }

                $fetchedArticles = $response->json('articles') ?? [];

                foreach ($fetchedArticles as $article) {
                    if (count($articles) >= 20) {
                        break;
                    }

                    $articles[] = [
                        'title' => $article['title'] ?? 'No Title',
                        'description' => $article['description'] ?? '',
                        'url' => $article['url'],
                        'source_name' => $article['source']['name'] ?? 'Unknown',
                        'published_at' => $article['publishedAt'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $this->info("Fetched articles for keyword: $keyword.");
                Log::info("Fetched articles for keyword: $keyword", ['count' => count($fetchedArticles)]);
            } catch (\Exception $e) {
                $this->error("Error fetching news for keyword: $keyword");
                Log::error("Error fetching news for keyword: $keyword", ['message' => $e->getMessage()]);
            }
        }

        if (!empty($articles)) {
            $this->info('Saving articles to the database...');
            Log::info('Saving articles to the database.', ['count' => count($articles)]);

            foreach (array_chunk($articles, 5) as $chunk) {
                News::insert($chunk);
            }

            $this->info('Successfully fetched and stored tech news articles.');
            Log::info('Successfully fetched and stored tech news articles.');
        } else {
            $this->error('No articles were fetched.');
            Log::warning('No articles were fetched.');
        }

        return 0;
    }
}
