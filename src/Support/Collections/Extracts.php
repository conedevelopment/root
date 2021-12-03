<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Exceptions\ExtractResolutionException;
use Cone\Root\Extracts\Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Extracts extends Collection
{
    /**
     * Filter the extracts that are available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Extract $extract) use ($request): bool {
                        return $extract->authorized($request);
                    })
                    ->values();
    }

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

    /**
     * Call the resolved callbacks on the extracts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function resolved(Request $request): void
    {
        $this->each(static function (Extract $extract) use ($request): void {
            $extract->resolved($request);
        });
    }
}
