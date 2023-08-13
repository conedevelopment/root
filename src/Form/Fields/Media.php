<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Models\Medium;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;

class Media extends File
{
    /**
     * Indicates if the component is async.
     */
    protected bool $async = true;

    /**
     * Indicates if the component is multiple.
     */
    protected bool $multiple = true;

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
     * Set the multiple attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }

    /**
     * Paginate the results.
     */
    public function paginate(Request $request): array
    {
        return $this->resolveRelatableQuery()
            ->latest()
            ->paginate($request->input('per_page'))
            ->withQueryString()
            ->setPath($this->replaceRoutePlaceholders($request->route()))
            ->through(function (Medium $related): FileOption {
                return $this->newOption($related, $this->resolveDisplay($related));
            })
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(mixed $value, string $label): FileOption
    {
        return parent::newOption($value, $label); // map pivot values using pivot fields
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'modalKey' => $this->getModalKey(),
            'config' => [
                'multiple' => $this->multiple,
            ],
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
