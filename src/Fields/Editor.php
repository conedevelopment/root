<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\URL;

class Editor extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRotues;
    }

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Editor';

    /**
     * The media field instance.
     *
     * @var \Cone\Root\Fields\Media
     */
    protected Media $media;

    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->media = Media::make(__('Media'), 'media');
    }

    /**
     * Configure the media field.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withMedia(Closure $callback): static
    {
        call_user_func_array($callback, [$this->media]);

        return $this;
    }

    /**
     * Register the routes using the given router.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->defaultRegisterRotues($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->media->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        //
    }

        /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'media_url' => URL::to($this->media->getUri()),
        ]);
    }
}
