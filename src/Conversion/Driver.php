<?php

declare(strict_types=1);

namespace Cone\Root\Conversion;

use Closure;
use Cone\Root\Models\Medium;
use Cone\Root\Support\Facades\Conversion;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;

abstract class Driver
{
    /**
     * The driver config.
     */
    protected array $config = [];

    /**
     * Create a new driver instance.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Perform the registered conversions on the medium.
     *
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function perform(Medium $medium): void
    {
        if (! Storage::disk($medium->disk)->exists($medium->getPath())) {
            throw new FileNotFoundException("The file located at [{$medium->getAbsolutePath()}] is not found.");
        }

        $conversions = [];

        foreach (Conversion::all() as $conversion => $callback) {
            $this->convert($medium, $conversion, $callback);

            $conversions[] = $conversion;
        }

        $medium->properties = array_replace(
            (array) $medium->properties,
            ['conversions' => $conversions]
        );

        $medium->save();
    }

    /**
     * Convert the medium using the given conversion.
     */
    abstract public function convert(Medium $medium, string $conversion, Closure $callback): void;
}
