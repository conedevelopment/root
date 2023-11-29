<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Models\Medium;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

class Editor extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.editor';

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
    public function __construct(string $label, Closure|string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->config = Config::get('root.editor', []);
        $this->height('350px');
        $this->hiddenOn(['index']);
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return str_replace('.', '-', $this->getRequestKey());
    }

    /**
     * Set the height style HTML attribute.
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
     * Configure the media field.
     */
    public function withMedia(Closure $callback = null): static
    {
        if (is_null($this->fields)) {
            $this->fields = new Fields();
        }

        if (is_null($this->media)) {
            $this->media = $this->newMediaField();

            $this->fields->register($this->media);
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
     * Make a custom media field.
     */
    protected function newMediaField(): Media
    {
        return new class($this->getModelAttribute()) extends Media
        {
            public function __construct(string $modelAttribute)
            {
                parent::__construct(__('Media'), $modelAttribute.'-media', static function (): MorphToMany {
                    return new MorphToMany(
                        Medium::proxy()->newQuery(),
                        new class() extends Model
                        {
                            //
                        },
                        'media',
                        'root_mediables',
                        'medium_id',
                        '_model_id',
                        'id',
                        'id'
                    );
                });

                $this->template = 'root::fields.editor.media';

                $this->multiple();
            }
        };
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        if (! is_null($this->media)) {
            $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
                $this->media->registerRoutes($request, $router);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'config' => $this->config,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'media' => $this->media?->toInput($request, $model),
        ]);
    }
}
