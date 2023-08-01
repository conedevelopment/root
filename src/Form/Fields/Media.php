<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Media extends File
{
    /**
     * Indicates if the component is async.
     */
    protected bool $async = true;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.media';

    /**
     * Get the modal key.
     */
    public function getModalKey(): string
    {
        return sprintf('%s-field-%s', $this->form->getKey(), $this->getKey());
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'modalKey' => $this->getModalKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->get('/', [MediaController::class, 'index']);
        $router->post('/', [MediaController::class, 'store']);
        $router->delete('/', [MediaController::class, 'destroy']);
    }
}
