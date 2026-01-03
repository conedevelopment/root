<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use JsonSerializable;

class OptGroup implements Arrayable, JsonSerializable
{
    use Conditionable;
    use HasAttributes;
    use Makeable;

    /**
     * The options within the opt group.
     *
     * @var list<Option>
     */
    protected array $options = [];

    /**
     * Create a new opt group instance.
     */
    public function __construct(protected string $label, array $options)
    {
        $this->options = array_map(function (Option|int|string $item, int|string $key): Option {
            return match (true) {
                $item instanceof Option => $item,
                default => new Option((string) $key, (string) $item),
            };
        }, $options, array_keys($options));
    }

    /**
     * Set the "disabled" HTML attribute.
     */
    public function disabled(bool $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the "selected" HTML attribute.
     */
    public function selected(bool|Closure $value = true): static
    {
        foreach ($this->options as $option) {
            $option->selected($value);
        }

        return $this;
    }

    /**
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'label' => $this->label,
            'options' => array_map(fn (Option $option): array => $option->toArray(), $this->options),
        ];
    }
}
