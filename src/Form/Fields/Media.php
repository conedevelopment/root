<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Illuminate\Routing\Router;

class Media extends File
{
    /**
     * Indicates if the component is async.
     */
    protected bool $async = true;

    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.media';

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
