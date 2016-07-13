<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class DownloadCalendar extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'calendar:download';

    protected $signature = 'calendar:download {url}';

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
        $this->info('URL: ' . $this->argument('url'));
        
        $this->copyRemoteFile();

        if (is_file($this->file) && is_readable($this->file)) {
            $this->info('File: ' . $this->file);
        }
    }


    public function copyRemoteFile()
    { 
        $url = explode('/', $this->argument('url'));
        $file = uniqid() . '_' . end($url);
        
        $this->file = storage_path('download') . DIRECTORY_SEPARATOR . $file;
        try {
            $response = $this->client->get($this->argument('url'), ['sink' => $this->file]);
            return true;
        } catch (Exception $e) {
            // Log the error or something
            return false;
        }
        
    }

    public static function cleanUp($term = '7 days')
    {
        $now = new \DateTime('now');
        $past_time = $now->modify('- ' . $term);

        $valid = \DateTime::getLastErrors();

        if ($valid['warning_count'] != 0 && $valid['error_count'] != 0) {
            error_log('Invalid date: ' . $term);
            return;
        }

        if (file_exists(storage_path('download'))) {
            foreach (new DirectoryIterator(storage_path('download')) as $fileInfo) {
                if (!$fileInfo->isDot() && time() - $fileInfo->getCTime() >= $past_time->getTimestamp()) {
                    unlink($fileInfo->getRealPath());
                }
            }
        }
    }

}
