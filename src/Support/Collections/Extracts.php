<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Exceptions\ExtractResolutionException;
use Cone\Root\Extracts\Extract;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Resolve the extract by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Extracts\Extract
     *
     * @throws \Cone\Root\Exceptions\ExtractResolutionException
     */
    public function resolve(string $key): Extract
    {
        $extract = $this->first(static function (Extract $extract) use ($key): bool {
            return $extract->getKey() === $key;
        });

        if (is_null($extract)) {
            throw new ExtractResolutionException("Unable to resolve extract with key [{$key}].");
        }

        return $extract;
    }
}
