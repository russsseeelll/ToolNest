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
        $keywordsList = [
            'education technology', 'edtech', 'AI in education', 'robotics in classrooms', 'smart classrooms',
            'infrastructure', 'NVIDIA', 'AI research', 'cloud computing', 'data centers',
            'machine learning', 'AR and VR', 'IoT', 'developer tools'
        ];
        $domains = 'techcrunch.com,thenextweb.com,wired.com,arstechnica.com,theverge.com,venturebeat.com';
        $from = now()->subDay()->toDateString();
        $to = now()->toDateString();

        $articles = [];
        $urls = []; // Keep track of unique URLs

        try {
            foreach ($keywordsList as $keywords) {
                $this->info("Searching with keywords: {$keywords}...");
                $response = Http::timeout(10)->get($url, [
                    'apiKey' => $apiKey,
                    'q' => $keywords,
                    'domains' => $domains,
                    'from' => $from,
                    'to' => $to,
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => 20 - count($articles),
                ]);

                if ($response->successful()) {
                    $fetchedArticles = $response->json('articles') ?? [];
                    foreach ($fetchedArticles as $article) {
                        // Avoid duplicates by checking the URL
                        if (!in_array($article['url'], $urls)) {
                            $articles[] = $article;
                            $urls[] = $article['url'];
                        }
                    }
                }

                if (count($articles) >= 20) {
                    break;
                }
            }

            if (empty($articles)) {
                $this->error('No articles found for the given search criteria.');
                return 1;
            }

            // Display articles before inserting
            $this->info('Fetched Articles:');
            foreach ($articles as $article) {
                $this->line('--------------------------------');
                $this->line('Title: ' . ($article['title'] ?? 'No Title'));
                $this->line('Description: ' . ($article['description'] ?? 'No Description'));
                $this->line('URL: ' . $article['url']);
                $this->line('Source: ' . ($article['source']['name'] ?? 'Unknown'));
                $this->line('Published At: ' . ($article['publishedAt'] ?? 'No Date'));
            }

            // Clear the news table
            $this->info('Truncating the news table...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            News::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // Insert articles into the database
            $this->info('Inserting articles into the database...');
            foreach ($articles as $article) {
                News::create([
                    'title' => $article['title'] ?? 'No Title',
                    'description' => $article['description'] ?? '',
                    'url' => $article['url'],
                    'source_name' => $article['source']['name'] ?? 'Unknown',
                    'published_at' => isset($article['publishedAt'])
                        ? date('Y-m-d H:i:s', strtotime($article['publishedAt']))
                        : now(),
                ]);
            }

            $this->info('Successfully fetched and stored tech news articles.');
        } catch (\Exception $e) {
            $this->error('Error fetching tech news: ' . $e->getMessage());
            Log::error('FetchTechNews command failed.', ['error' => $e->getMessage()]);
            return 1;
        }

        return 0;
    }
}
