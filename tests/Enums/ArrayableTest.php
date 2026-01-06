<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Enums;

use Cone\Root\Enums\Arrayable;
use Cone\Root\Tests\TestCase;

final class ArrayableTest extends TestCase
{
    public function test_enum_can_be_converted_to_array(): void
    {
        $array = TestEnum::toArray();

        $this->assertSame([
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
        ], $array);
    }
}

enum TestEnum: string
{
    use Arrayable;

    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case PENDING = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::PENDING => 'Pending',
        };
    }
}
