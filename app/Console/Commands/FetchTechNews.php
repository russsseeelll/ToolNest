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
    protected $description = 'Fetch and store 20 random tech news articles focused on education and new technologies, broadening search if necessary';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching tech news from the API...');

        $apiKey = '38b207b9cf2b49f4ac9f78b0951d9a28';
        $url = 'https://newsapi.org/v2/everything';
        $primaryKeywords = 'education technology, edtech, new technology, AI in education, VR in education, robotics in classrooms, future of learning, smart classrooms';
        $secondaryKeywords = 'infrastructure, NVIDIA, AI research, cloud computing, data centers, semiconductor advancements, 5G technology, quantum computing, machine learning, AR and VR, IoT, tech startups, developer tools, networking';
        $domains = 'edtechmagazine.com,educationaltechnology.net,techlearning.com,elearningindustry.com,insidehighered.com,thejournal.com,techcrunch.com,thenextweb.com,wired.com,arstechnica.com,theverge.com,venturebeat.com,gizmodo.com,cnet.com,zdnet.com,techradar.com,digitaltrends.com';
        $from = now()->subDay()->toDateString();
        $to = now()->toDateString();

        try {
            $articles = [];

            // First pass: Primary keywords
            $response = Http::timeout(10)->get($url, [
                'apiKey' => $apiKey,
                'q' => $primaryKeywords,
                'domains' => $domains,
                'from' => $from,
                'to' => $to,
                'language' => 'en',
                'sortBy' => 'publishedAt',
                'pageSize' => 20,
            ]);

            if ($response->successful()) {
                $articles = $response->json('articles') ?? [];
            }

            // If not enough articles, broaden the search
            if (count($articles) < 20) {
                $this->info('Broadening search with secondary keywords...');

                $response = Http::timeout(10)->get($url, [
                    'apiKey' => $apiKey,
                    'q' => $secondaryKeywords,
                    'domains' => $domains,
                    'from' => $from,
                    'to' => $to,
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => 20 - count($articles),
                ]);

                if ($response->successful()) {
                    $additionalArticles = $response->json('articles') ?? [];
                    $articles = array_merge($articles, $additionalArticles);
                }
            }

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
