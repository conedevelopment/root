<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Field;
use Cone\Root\Form\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class FilterForm extends Form
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.filters.form';

    /**
     * Create a new form instance.
     */
    public function __construct(Model $model, string $action, string $apiUri = null)
    {
        parent::__construct($model, $action, $apiUri);

        $this->method('GET');
        $this->setAttribute('class', 'app-card__actions');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'search' => $this->resolveFields($request)->first(function (Field $field): bool {
                        return $field instanceof SearchField;
                    }),
                    'fields' => $this->resolveFields($request)->reject(function (Field $field): bool {
                        return $field instanceof SearchField;
                    })->all(),
                ];
            }
            ));
    }
}
