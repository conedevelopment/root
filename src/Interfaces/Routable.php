<?php

namespace Cone\Root\Interfaces;

use Illuminate\Http\Request;

interface Routable
{
    /**
     * Set the URI attribute
     *
     * @param  string|null  $uri
     * @return void
     */
    public function setUri(?string $uri = null): void;

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string;

    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function routes(Request $request): void;
}
