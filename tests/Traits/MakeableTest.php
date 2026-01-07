<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Traits;

use Cone\Root\Tests\TestCase;
use Cone\Root\Traits\Makeable;

final class MakeableTest extends TestCase
{
    public function test_makeable_creates_new_instance(): void
    {
        $instance = TestMakeable::make('param1', 'param2');

        $this->assertInstanceOf(TestMakeable::class, $instance);
        $this->assertSame('param1', $instance->param1);
        $this->assertSame('param2', $instance->param2);
    }

    public function test_makeable_works_without_parameters(): void
    {
        $instance = TestMakeableNoParams::make();

        $this->assertInstanceOf(TestMakeableNoParams::class, $instance);
    }
}

class TestMakeable
{
    use Makeable;

    public function __construct(
        public string $param1,
        public string $param2
    ) {}
}

class TestMakeableNoParams
{
    use Makeable;

    public function __construct() {}
}
