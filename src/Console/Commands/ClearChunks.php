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
     */
    public function handle(): void
    {
        $now = time();

        $expiration = Config::get('root.media.chunk_expiration', 1440) * 60;

        $count = 0;

        foreach (Storage::disk('local')->allFiles(Config::get('root.media.upload_dir')) as $file) {
            $info = new SplFileInfo(Storage::disk('local')->path($file));

            if ($now - $info->getMTime() >= $expiration) {
                Storage::disk('local')->delete($file);

                $count++;
            }
        }

        $this->info(sprintf('%d chunks are cleared!', $count));
    }
}
