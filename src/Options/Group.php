<?php

namespace Cone\Root\Options;

use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class Group
{
    use AsForm;
    use Authorizable;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->plural()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->headline()->plural()->value());
    }

    /**
     * Make a new Eloquent query instance.
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with)->withCount($this->withCount);
    }

    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request): void
    {
        $this->validateFormRequest($request);

        try {
            DB::beginTransaction();

            $this->resolveFields($request)
                ->authorized($request)
                ->visible($request->isMethod('POST') ? 'create' : 'update')
                ->persist($request, $model);

            DB::commit();
        } catch (Throwable $exception) {
            report($exception);

            DB::rollBack();

            throw new SaveFormDataException($exception->getMessage());
        }
    }
}
