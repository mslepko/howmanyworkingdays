<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use ICal;
use Log;
use App\Holiday;

class ParseICSCalendar extends Command 
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'calendar:parse';

    protected $signature = 'calendar:parse {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse ICS calendar';

    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $file = $this->argument('file');

        if (empty($file)) {
           if (!($file = $this->getNewestFile(storage_path('download/ukical')))) {
                $this->error('Set file you want to parse');
           }
        }

        $ical = new ICal($file);
        
        $events = $ical->events();

        if (!empty($events)) {
            foreach ($events as $event) {
                if (!$event['UID'] && !$event['SUMMARY']) {
                    continue;
                }

                $uid = !empty($event['UID']) ? $event['UID'] : str_slug($events['SUMMARY']);
                Log::info('Processing uid: ' . $uid);
                try {
                $holiday = Holiday::firstOrNew([
                    'uid' => $uid,
                ]);

                $holiday->date_start = date('Y-m-d', $ical->iCalDateToUnixTimestamp($event['DTSTART']));
                $holiday->date_end = date('Y-m-d', $ical->iCalDateToUnixTimestamp($event['DTEND']));
                $holiday->description = !empty($event['DESCRIPTION']) ? $event['DESCRIPTION'] : '';
                $holiday->summary = $event['SUMMARY'];

                $holiday->save();

                Log::info('Updated');
                } Catch (\Exception $e) {
                    Log::error('Not updated. Error: ' . $e->getMessage());
                }

            }
            
        }
    }

    public function getNewestFile($dirname)
    {
        $path = false;
        $timestamp = null;

        $dir = new \DirectoryIterator($dirname);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                if ($fileinfo->getMTime() > $timestamp) {
                    $path = $fileinfo->getPathname();
                    $timestamp = $fileinfo->getMTime();
                }
            }
        }
    
        return $path;
    }

}
