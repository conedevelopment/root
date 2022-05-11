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
     * @var \Cone\Root\Fields\Media|null
     */
    protected ?Media $media = null;

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
        }

        if (! is_null($callback)) {
            call_user_func_array($callback, [$this->media]);
        }

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

        if (! is_null($this->media)) {
            $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
                $this->media->registerRoutes($request, $router);
            });
        }
    }

    /**
     * Get the default quill config.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function getDefaultConfig(Request $request, Model $model): array
    {
        return [
            'modules' => [
                'toolbar' => [
                    'container' => [
                        [['header' => [1, 2, 3, 4, false]]],
                        ['bold', 'italic', 'underline'],
                        [['list' => 'ordered'], ['list' => 'bullet'], ['align' => []]],
                        ['link'],
                        ['clean'],
                    ],
                    'handlers' => (object) [],
                ],
                'clipboard' => ['matchVisual' => false],
            ],
            'theme' => 'snow',
            'formats' => ['header', 'align', 'bold', 'underline', 'italic', 'list', 'link'],
            'placeholder' => $this->placeholder,
        ];
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
            'config' => $this->getDefaultConfig($request, $model),
            'media_url' => is_null($this->media) ? null : URL::to($this->media->getUri()),
            'with_media' => ! is_null($this->media),
        ]);
    }
}
