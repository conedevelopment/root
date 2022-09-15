<?php

declare(strict_types = 1);

namespace Cone\Root\Interfaces\Support\Collections;

use Cone\Root\Http\Requests\RootRequest;

interface Resources
{
    /**
     * Filter the available resources.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Interfaces\Support\Collections\Resources
     */
    public function available(RootRequest $request): static;
}
