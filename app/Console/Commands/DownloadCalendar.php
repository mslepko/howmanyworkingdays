<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class DownloadCalendar extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'download:calendar';

    protected $signature = 'download:calendar {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download calendar file from url.';

    /**
     * The Guzzle HTTP Client
     *
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        parent::__construct();

        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->info('it works! ' . $this->argument('url'));
    }

}
