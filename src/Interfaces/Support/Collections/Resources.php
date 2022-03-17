<?php

namespace Cone\Root\Interfaces\Support\Collections;

use Illuminate\Http\Request;

interface Resources
{
    /**
     * Filter the available resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Interfaces\Support\Collections\Resources
     */
    public function available(Request $request): static;
}
