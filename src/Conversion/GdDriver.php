<?php

namespace Cone\Root\Conversion;

use Closure;
use Cone\Root\Models\Medium;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GdDriver extends Driver
{
    /**
     * {@inheritdoc}
     */
    public function perform(Medium $medium): void
    {
        File::ensureDirectoryExists(Storage::disk('local')->path('root-tmp'));

        parent::perform($medium);
    }

    /**
     * {@inheritdoc}
     */
    public function convert(Medium $medium, string $conversion, Closure $callback): void
    {
        $image = $this->createImage($medium);

        call_user_func_array($callback, [$image]);

        $image->save();

        Storage::disk($medium->disk)->move(
            $image->getPath(), $medium->getPath($conversion)
        );

        $image->destroy();
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
