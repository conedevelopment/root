<?php

namespace Cone\Root\Traits;

use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

trait AsForm
{
    use ResolvesFields;

    /**
     * The error bag.
     */
    protected string $errorBag = 'default';

    /**
     * The form errors.
     */
    protected ?MessageBag $errors = null;

    /**
     * Handle the request.
     */
    abstract public function handleFormRequest(Request $request, Model $model): void;

    /**
     * Validate the request.
     */
    public function validateFormRequest(Request $request, Model $model): array
    {
        return $request->validateWithBag(
            $this->errorBag,
            $this->resolveFields($request)->mapToValidate($request, $model)
        );
    }

    /**
     * Get the errors.
     */
    public function errors(Request $request): MessageBag
    {
        if (is_null($this->errors)) {
            $this->errors = $request->session()->get('errors', new ViewErrorBag)->getBag($this->errorBag);
        }

        return $this->errors;
    }
}
