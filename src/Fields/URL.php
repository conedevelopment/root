<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Root;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class URL extends Text
{
    /**
     * The text resolver callback.
     */
    protected Closure $textResolver;

    /**
     * The link attributes.
     */
    protected array $linkAttributes = [];

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('url');

        $this->textResolver = fn (): string => 'URL';
    }

    /**
     * Set the text resolver callback.
     */
    public function text(Closure|string $value): static
    {
        $this->textResolver = is_string($value) ? fn (): string => $value : $value;

        return $this;
    }

    /**
     * Set the download attribute.
     */
    public function download(string|Closure $filename = ''): static
    {
        $this->linkAttributes['download'] = $filename;

        return $this;
    }

    /**
     * Set the target attribute.
     */
    public function target(string|Closure $target): static
    {
        $this->linkAttributes['target'] = $target;

        return $this;
    }

    /**
     * Set the rel attribute.
     */
    public function rel(string|Closure $rel): mixed
    {
        $this->linkAttributes['rel'] = $rel;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): ?string {
                if (is_null($value)) {
                    return $value;
                }

                $attributes = array_merge($this->linkAttributes, [
                    'href' => $value,
                    'title' => $value,
                    'data-turbo' => $this->isExternal($value) ? 'false' : null,
                    'target' => $this->isExternal($value) ? '_blank' : ($this->linkAttributes['target'] ?? null),
                ]);

                $attributes = array_map(
                    static function (string|Closure|null $attribute, string $name) use ($request, $model, $value): string {
                        $attribute = (string) match (true) {
                            $attribute instanceof Closure => call_user_func_array($attribute, [$request, $model, $value]),
                            default => $attribute,
                        };

                        return sprintf('%s="%s"', $name, $attribute);
                    },
                    array_values($attributes),
                    array_keys($attributes)
                );

                return sprintf(
                    '<a %s>%s</a>',
                    implode(' ', $attributes),
                    call_user_func_array($this->textResolver, [$model])
                );
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Determine if the given URL is external.
     */
    public function isExternal(string $value): bool
    {
        $root = Root::instance();

        [$domain, $path] = [$root->getDomain(), $root->getPath()];

        $url = parse_url($value);

        return $domain !== ($url['host'] ?? null)
            && ! str_starts_with(ltrim($url['path'] ?? '', '/'), ltrim($path, '/'));
    }
}
