<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleRootRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'root::app';

    /**
     * Defines the props that are shared by default.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'alerts' => static function () use ($request): array {
                return array_values(Arr::wrap($request->session()->get('alerts')));
            },
            'csrf_token' => static function () use ($request): string {
                return $request->session()->token();
            },
            'url' => Str::start($request->path(), '/'),
        ]);
    }
}
