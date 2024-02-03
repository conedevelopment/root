<?php

namespace Cone\Root\Console\Commands;

use Cone\Root\Models\Medium;
use Illuminate\Console\Command;

class ClearMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'root:clear-media';

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

        Medium::proxy()->newQuery()->cursor()->each(static function (Medium $medium) use (&$count): void {
            $medium->delete();

            $count++;
        });

        $this->info(sprintf('%d media are cleared!', $count));
    }
}
