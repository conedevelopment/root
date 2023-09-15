<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Fields\Options\RepeaterOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Repeater extends Fieldset
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.repeater';

    /**
     * The option fields resolver.
     */
    protected ?Closure $optionFieldsResolver = null;

    /**
     * Get the option name.
     */
    public function getOptionName(): string
    {
        return Str::singular($this->label);
    }

    /**
     * Get the add new option label.
     */
    public function getAddNewOptionLabel(): string
    {
        return __('Add :name', ['name' => $this->getOptionName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return array_values((array) parent::getValueForHydrate($request));
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if (! is_null($this->apiUri)) {
            $field->setApiUri(sprintf('%s/%s', $this->apiUri, $field->getUriKey()));
        }

        $field->setModelAttribute(
            sprintf('%s.*.%s', $this->getModelAttribute(), $field->getModelAttribute())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withFields(Closure $callback): static
    {
        $this->optionFieldsResolver = function (Model $tmpModel) use ($callback): Fields {
            $fields = new Fields($this->form);

            $fields->hidden(__('Key'), '_key');

            App::call(static function (Request $request) use ($callback, $fields): void {
                call_user_func_array($callback, [$request, $fields]);
            });

            $fields->each(function (Field $field) use ($tmpModel): void {
                $attribute = sprintf(
                    '%s.%s.%s',
                    $this->getModelAttribute(),
                    $tmpModel->getAttribute('_key'),
                    $key = $field->getModelAttribute()
                );

                $field->setModelAttribute($attribute)
                    ->name($attribute)
                    ->id($attribute)
                    ->value(function () use ($tmpModel, $key): mixed {
                        return $tmpModel->getAttribute($key);
                    });
            });

            return $fields;
        };

        return parent::withFields($callback);
    }

    /**
     * Make a new temporary model for the option.
     */
    public function newTemporaryModel(array $attributes = []): Model
    {
        $model = new class() extends Model {};

        return $model->forceFill(array_replace(
            ['_key' => Str::uuid()],
            $attributes
        ));
    }

    /**
     * Resolve the repeater options.
     */
    public function resolveOptions(Request $request): array
    {
        $value = (array) $this->resolveValue($request);

        return array_map(function (array $option, int $key): RepeaterOption {
            return $this->newOption($option, sprintf('#%d', ++$key));
        }, $value, array_keys($value));
    }

    /**
     * Make a new option instance.
     */
    public function newOption(array $value, string $label): mixed
    {
        return (new RepeaterOption($this->newTemporaryModel($value), $label))
            ->when(! is_null($this->optionFieldsResolver), function (RepeaterOption $option): void {
                $option->withFields(call_user_func_array($this->optionFieldsResolver, [$option->model]));
            });
    }

    /**
     * Build a new option.
     */
    public function buildOption(Request $request): JsonResponse
    {
        return new JsonResponse(
            $this->newOption([], sprintf('#%d', mt_rand(10, 20)))->toRenderedArray()
        );
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
                    'addNewLabel' => $this->getAddNewOptionLabel(),
                    'options' => array_map(static function (RepeaterOption $option): array {
                        return $option->toRenderedArray();
                    }, $this->resolveOptions($request)),
                    'url' => $this->getApiUri(),
                    'config' => [],
                ];
            })
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return match ($request->method()) {
            'POST' => $this->buildOption($request),
            default => parent::toResponse($request),
        };
    }
}
