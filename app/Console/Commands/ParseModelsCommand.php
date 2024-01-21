<?php

namespace App\Console\Commands;

use App\Models\Model;
use Goutte\Client;
use Illuminate\Console\Command;

class ParseModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:models';
    protected $description = 'Parse Audi models and store in the database';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.drom.ru/catalog/audi/');

        $models = $crawler->filterXPath('//a[@class="e64vuai0 css-1i48p5q e104a11t0"]/text()')->each(function ($node) {
            return $node->text();
        });

        foreach ($models as $model) {
            Model::create(['name' => $model]);
        }

        $this->info('Models parsed and stored successfully.');
    }
}
