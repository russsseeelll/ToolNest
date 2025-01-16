<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\FetchTechNews;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fetch:tech-news', function () {
    Artisan::call(FetchTechNews::class);
})->purpose('Fetch and store the latest tech news articles');
