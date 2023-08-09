<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Models\Medium;
use Cone\Root\Models\User;
use Cone\Root\Traits\RegistersRoutes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;

class Editor extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.editor';

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
    public function __construct(Form $form, string $label, string $key = null)
    {
        parent::__construct($form, $label, $key);

        $this->config = Config::get('root.editor', []);
        $this->height('350px');
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
        if (is_null($this->media)) {
            $this->media = $this->newMediaField();
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
        return new class($this->form, __('Media'), $this->getKey().'-media', static function (): MorphToMany {
            return new MorphToMany(
                Medium::proxy()->newQuery(),
                User::proxy(),
                'media',
                'root_mediables',
                'medium_id',
                'user_id',
                'id',
                'id'
            );
        }) extends Media {
            protected string $template = 'root::form.fields.editor.media';
        };
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        if (! is_null($this->media)) {
            $router->prefix($this->getUriKey())->group(function (Router $router): void {
                $this->media->registerRoutes($router);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'config' => $this->config,
            'media' => $this->media,
        ]);
    }
}
