<?php

namespace App\Console\Commands;

use App\Models\Generation;
use App\Models\Model;
use Goutte\Client;
use Illuminate\Console\Command;

class ParseGeneration extends Command
{
    protected $signature = 'parse:audi-generations';
    protected $description = 'Parse Audi generations and save to database';

    public function handle()
    {
        $models = Model::all();
        $client = new Client();

        foreach ($models as $model) {
            $modelName = $model->name;
            $baseUrl = 'https://www.drom.ru/catalog/audi/' . strtolower(str_replace(' ', '-', $modelName));

            $generationCrawler = $client->request('GET', $baseUrl);

            $generationCrawler->filter('.css-pyemnz')->each(function ($node) use ($model, $baseUrl) {
                $market = $node->filter('.css-112idg0')->text();
                $this->info("Market: $market");

                $items = $node->filter('.css-btm8d5');

                $items->each(function ($item) use ($model, $baseUrl, $market) {
                    $nameNode = $item->filterXPath('//span[@class="css-1089mxj e1ei9t6a2"]/text()');
                    $name = $nameNode->count() ? $nameNode->text() : '';

                    $svgName = $this->extractSvgName($nameNode->html());

                    $finalName = $svgName ? $svgName : $name;
                    $generationType = $item->filter('[data-ftid="component_article_extended-info"] div')->eq(0)->text();
                    $image = $item->filter('.css-c4i2qy')->attr('data-src');
                    $href = $item->filter('a')->attr('href');

                    $pageUrl = $this->buildPageUrl($baseUrl, $href);

                    $generationData = [
                        'market' => $market,
                        'name' => $finalName,
                        'period' => $generationType,
                        'path_to_image' => $image,
                        'path_to_page' => $pageUrl,
                    ];

                    $model->generations()->create($generationData);

                    $this->info("Generation Data for $finalName saved successfully!");
                });
            });

            $this->info("Audi generations for $modelName parsed and saved successfully!");
        }
    }

    private function extractSvgName($htmlContent)
    {
        preg_match('/<span[^>]*>(.*?)<\/span>/i', $htmlContent, $matches);
        return $matches[1] ?? '';
    }

    private function buildPageUrl($baseUrl, $href)
    {
        return $baseUrl . '/' . $href;
    }
}
