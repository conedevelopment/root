<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface Form
{
    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request, Model $model): void;

    /**
     * Validate the request.
     */
    public function validateFormRequest(Request $request, Model $model): array;

    /**
     * Get the errors.
     */
    public function errors(Request $request): MessageBag;
}
