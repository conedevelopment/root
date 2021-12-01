<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Exceptions\ExtractResolutionException;
use Cone\Root\Extracts\Extract;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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

    /**
     * Register the extract routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $uri
     * @return void
     */
    public function routes(Request $request, ?string $uri = null): void
    {
        Route::prefix('extracts')->group(function () use ($request, $uri): void {
            $this->each(static function (Extract $extract) use ($request, $uri): void {
                if (! App::routesAreCached()) {
                    $extract->routes($request);
                }

                $extract->setUri("{$uri}/extracts/{$extract->getKey()}");
            });
        });
    }
}
