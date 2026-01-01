<?php

declare(strict_types=1);

namespace Cone\Root\Enums;

trait Arrayable
{
    /**
     * Get the label of the enum case.
     */
    abstract public function label(): string;

    /**
     * Convert to array.
     */
    public static function toArray(): array
    {
        return array_reduce(self::cases(), function (array $cases, self $case): array {
            return array_merge(
                $cases,
                [$case->value => $case->label()]
            );
        }, []);
    }
}
