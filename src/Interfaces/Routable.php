<?php

namespace Cone\Root\Interfaces;

use Illuminate\Http\Request;

interface Routable
{
    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function routes(Request $request): void;

    /**
     * Set the URI attribute
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(string $uri): void;

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string;
}
