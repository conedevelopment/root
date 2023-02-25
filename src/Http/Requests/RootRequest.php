<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Support\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

class RootRequest extends Request
{
    /**
     * Validate the request.
     *
     * @param  array  ...$parameters
     */
    public function validate(array $rules, ...$parameters): void
    {
        if (! $this->header('X-Inertia')) {
            parent::validate($rules, ...$parameters);
        }

        try {
            App::make(Factory::class)->validate($this->all(), $rules, ...$parameters);
        } catch (ValidationException $exception) {
            $this->session()->flash(
                'alerts.validation-failed', Alert::error(__('The submitted form data is invalid!'))
            );

            throw $exception;
        }
    }

    /**
     * Get the resolved component.
     */
    public function resolved(): mixed
    {
        return $this->route('resolved');
    }
}
