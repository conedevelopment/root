<?php

namespace Cone\Root\Conversion;

use Cone\Root\Models\Medium;
use Cone\Root\Support\Facades\Conversion;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GdDriver extends Driver
{
    /**
     * Perform the registered conversions on the medium.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Cone\Root\Models\Medium
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function perform(Medium $medium): Medium
    {
        if (! Storage::disk($medium->disk)->exists($medium->getPath())) {
            throw new FileNotFoundException("The file located at [{$medium->getAbsolutePath()}] is not found.");
        }

        File::ensureDirectoryExists(Storage::disk('local')->path('root-tmp'));

        foreach (Conversion::all() as $conversion => $callback) {
            $image = $this->createImage($medium);

            call_user_func_array($callback, [$image]);

            $image->save();

            Storage::disk($medium->disk)->move(
                $image->getPath(), $medium->getPath($conversion)
            );

            $image->destroy();
        }

        return $medium;
    }

    /**
     * Create a new image instance.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Cone\Root\Conversion\Image
     */
    protected function createImage(Medium $medium): Image
    {
        $image = new Image($medium);

        return $image->setQuality(
            $this->config['quality'] ?? 70
        );
    }
}
