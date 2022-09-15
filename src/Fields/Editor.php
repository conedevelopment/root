<?php

declare(strict_types = 1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

class Editor extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRotues;
    }

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Editor';

    /**
     * The media field instance.
     *
     * @var \Cone\Root\Fields\Media|null
     */
    protected ?Media $media = null;

    /**
     * The editor config.
     *
     * @var array
     */
    protected array $config = [];

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

        $this->config = Config::get('root.editor', []);
    }

    /**
     * Set the configuration.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withConfig(Closure $callback): static
    {
        $this->config = call_user_func_array($callback, [$this->config]);

        return $this;
    }

    /**
     * Get the editor configuration.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Configure the media field.
     *
     * @param  \Closure|null  $callback
     * @return $this
     */
    public function withMedia(?Closure $callback = null): static
    {
        if (is_null($this->media)) {
            $this->media = Media::make(__('Media'), 'media');

            $this->config['modules']['toolbar']['container'][3][] = 'image';

            $this->config['formats'][] = 'image';
        }

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->media]);
        }

        return $this;
    }

    /**
     * Get the media field.
     *
     * @return \Cone\Root\Fields\Media|null
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * Register the routes using the given router.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $this->defaultRegisterRotues($request, $router);

        if (! is_null($this->media)) {
            $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
                $this->media->registerRoutes($request, $router);
            });
        }
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'config' => $this->config,
            'media_url' => is_null($this->media) ? null : $this->media->resolveUri($request),
            'with_media' => ! is_null($this->media),
        ]);
    }
}
