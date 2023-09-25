<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Models\Medium;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Config;

class Editor extends Field
{
    use ResolvesFields;

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
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->config = Config::get('root.editor', []);
        $this->height('350px');
    }

    /**
     * {@inheritdoc}
     */
    public function setApiUri(string $apiUri): static
    {
        if (! is_null($this->media)) {
            $this->media->setApiUri(
                sprintf('%s/%s', $apiUri, $this->media->getUriKey())
            );
        }

        return parent::setApiUri($apiUri);
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
     * Create a new fields collection.
     */
    protected function newFieldsCollection(): Fields
    {
        return new Fields($this->form);
    }

    /**
     * Configure the media field.
     */
    public function withMedia(Closure $callback = null): static
    {
        if (is_null($this->fields)) {
            $this->fields = $this->newFieldsCollection();
        }

        if (is_null($this->media)) {
            $this->media = $this->newMediaField();

            $this->fields->push($this->media);
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
        return new class($this->form, $this->getModelAttribute()) extends Media
        {
            public function __construct(Form $form, string $modelAttribute)
            {
                parent::__construct($form, __('Media'), $modelAttribute.'-media', static function (): MorphToMany {
                    return new MorphToMany(
                        Medium::proxy()->newQuery(),
                        new class() extends Model
                        {
                        },
                        'media',
                        'root_mediables',
                        'medium_id',
                        '_model_id',
                        'id',
                        'id'
                    );
                });

                $this->template = 'root::form.fields.editor.media';

                $this->multiple();
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'config' => $this->config,
            'media' => $this->media,
        ]);
    }
}
