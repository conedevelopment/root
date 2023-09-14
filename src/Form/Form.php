<?php

namespace Cone\Root\Form;

use Cone\Root\Form\Fields\Field;
use Cone\Root\Form\Fields\Fields;
use Cone\Root\Support\Element;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\ViewErrorBag;

class Form extends Element
{
    use Conditionable;
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.form';

    /**
     * The form model.
     */
    public readonly Model $model;

    /**
     * The error bag.
     */
    protected string $errorBag = 'default';

    /**
     * The form errors.
     */
    protected ?MessageBag $errors = null;

    /**
     * The API URI.
     */
    protected ?string $apiUri = null;

    /**
     * The form method.
     */
    protected string $method = 'POST';

    /**
     * Create a new form instance.
     */
    public function __construct(Model $model, string $action, string $apiUri = null)
    {
        $this->model = $model;
        $this->apiUri = $apiUri;

        $this->action($action);
        $this->method($model->exists ? 'PATCH' : 'POST');
        $this->id(Str::random(10));
        $this->autocomplete('off');
    }

    /**
     * Set the "enctype" HTML attribute.
     */
    public function enctype(string $value): static
    {
        return $this->setAttribute('enctype', $value);
    }

    /**
     * Set the "autocomplete" HTML attribute.
     */
    public function autocomplete(string $value): static
    {
        return $this->setAttribute('autocomplete', $value);
    }

    /**
     * Set the "method" HTML attribute.
     */
    public function method(string $value): static
    {
        $this->method = $value;

        return $this->setAttribute('method', $value === 'GET' ? 'GET' : 'POST');
    }

    /**
     * Set the "action" HTML attribute.
     */
    public function action(string $value): static
    {
        $this->method = $value;

        return $this->setAttribute('action', $value);
    }

    /**
     * Handle the incoming form request.
     */
    public function handle(Request $request): void
    {
        $this->validate($request);

        $this->resolveFields($request)->persist($request);

        $this->model->save();
    }

    /**
     * Validate the incoming request.
     */
    public function validate(Request $request): array
    {
        return $request->validateWithBag(
            $this->errorBag,
            $this->resolveFields($request)->mapToValidate($request)
        );
    }

    /**
     * Set the validation error bag.
     */
    public function errorBag(string $value): static
    {
        $this->errorBag = $value;

        return $this;
    }

    /**
     * Get the errors for the form.
     */
    public function errors(Request $request): MessageBag
    {
        if (is_null($this->errors)) {
            $this->errors = $request->session()->get('errors', new ViewErrorBag())->getBag($this->errorBag);
        }

        return $this->errors;
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if (! is_null($this->apiUri)) {
            $field->setApiUri(sprintf('%s/%s', $this->apiUri, $field->getUriKey()));
        }
    }

    /**
     * Create a new fields collection.
     */
    protected function newFieldsCollection(): Fields
    {
        return new Fields($this);
    }

    /**
     * Conver the form to an array.
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'errors' => $this->errors($request),
                    'fields' => $this->resolveFields($request)->all(),
                    'method' => $this->method,
                ];
            })
        );
    }
}
