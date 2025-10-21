<?php

declare(strict_types=1);

namespace Cone\Root\Console\Commands;

use Cone\Root\Models\Medium;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:clear-media {--all : Delete all the media and files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the medium models and files';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = 0;

        $all = $this->option('all');

        Medium::proxy()
            ->newQuery()
            ->cursor()
            ->each(static function (Medium $medium) use (&$count, $all): void {
                if ($all || ! Storage::disk($medium->disk)->exists($medium->getPath())) {
                    $medium->delete();

                    $count++;
                }
            });

        $this->info(sprintf('%d media have been deleted!', $count));
    }
}
