<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use Illuminate\Support\Facades\Http;

class FetchTechNews extends Command
{
    protected $signature = 'fetch:tech-news';
    protected $description = 'Fetch and store the latest tech news articles';

    public function handle()
    {
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

        // Clear existing news
        News::truncate();

        $articles = [];
        shuffle($keywords);

        foreach ($keywords as $keyword) {
            if (count($articles) >= 20) {
                break;
            }

            $response = Http::get($url, [
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
                    ];
                }
            }
        }

        // Save articles to the database
        foreach ($articles as $article) {
            News::create($article);
        }

        $this->info('Successfully fetched and stored 20 tech news articles.');
    }
}
