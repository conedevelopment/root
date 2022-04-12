<?php

namespace Cone\Root\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

class ClearChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:clear-chunks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the expired file chunks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $now = time();

        $expiration = Config::get('root.media.chunk_expiration', 1440) * 60;

        foreach (Storage::disk('local')->allFiles('root-chunks') as $file) {
            $info = new SplFileInfo(Storage::disk('local')->path($file));

            if ($now - $info->getMTime() >= $expiration) {
                Storage::disk('local')->delete($file);
            }
        }

        $this->info('File chunks are cleared!');

        return Command::SUCCESS;
    }
}
