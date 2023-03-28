<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Models\Medium;
use Cone\Root\Models\User;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

class Editor extends Field implements Routable
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRotues;
    }

    /**
     * The Vue component.
     */
    protected string $component = 'Editor';

    /**
     * The media field instance.
     */
    protected ?Media $media = null;

    /**
     * The editor config.
     */
    protected array $config = [];

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->config = Config::get('root.editor', []);
        $this->height('350px');
    }

    /**
     * Set the height style attribute.
     */
    public function height(string $value): static
    {
        return $this->setAttribute('style.height', $value);
    }

    /**
     * Set the configuration.
     */
    public function withConfig(Closure $callback): static
    {
        $this->config = call_user_func_array($callback, [$this->config]);

        return $this;
    }

    /**
     * Get the editor configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->name;
    }

    /**
     * Configure the media field.
     */
    public function withMedia(?Closure $callback = null): static
    {
        if (is_null($this->media)) {
            $this->media = Media::make(__('Media'), 'media', static function (): MorphToMany {
                return new MorphToMany(
                    Medium::proxy()->newQuery(),
                    User::proxy(),
                    'media',
                    'root_media',
                    'medium_id',
                    'user_id',
                    'id',
                    'id'
                );
            });
        }

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->media]);
        }

        return $this;
    }

    /**
     * Get the media field.
     */
    public function getMedia(): ?Media
    {
        return $this->media;
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRotues($router);

        if (! is_null($this->media)) {
            $router->prefix($this->getUriKey())->group(function (Router $router): void {
                $this->media->registerRoutes($router);
            });
        }
    }

    /**
     * Get the input representation of the field.
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'config' => $this->config,
            'media' => is_null($this->media) ? null : $this->media->toInput($request, $model),
        ]);
    }
}
