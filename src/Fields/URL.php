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
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = fn (Request $request, Model $model, mixed $value): ?string => is_null($value) ? $value : sprintf(
                '<a href="%1$s" title="%1$s"%2$s>%3$s</a>',
                $value,
                $this->isExternal($value) ? ' data-turbo="false" target="_blank"' : '',
                call_user_func_array($this->textResolver, [$model])
            );
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
