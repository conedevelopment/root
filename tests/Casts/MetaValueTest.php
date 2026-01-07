<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Casts;

use Cone\Root\Casts\MetaValue;
use Cone\Root\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

final class MetaValueTest extends TestCase
{
    protected MetaValue $cast;

    protected Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new MetaValue;

        $this->model = new class extends Model
        {
            protected $guarded = [];
        };
    }

    public function test_it_returns_null_when_value_is_null(): void
    {
        $result = $this->cast->get($this->model, 'test_key', null, []);

        $this->assertNull($result);
    }

    public function test_it_decodes_json_value(): void
    {
        $jsonValue = json_encode(['foo' => 'bar', 'baz' => 'qux']);

        $result = $this->cast->get($this->model, 'test_key', $jsonValue, []);

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $result);
    }

    public function test_it_returns_string_value_when_json_decode_fails(): void
    {
        $result = $this->cast->get($this->model, 'test_key', 'plain string', []);

        $this->assertSame('plain string', $result);
    }

    public function test_it_returns_null_for_storage_when_value_is_null(): void
    {
        $result = $this->cast->set($this->model, 'test_key', null, []);

        $this->assertNull($result);
    }

    public function test_it_returns_string_value_for_storage(): void
    {
        $result = $this->cast->set($this->model, 'test_key', 'test value', []);

        $this->assertSame('test value', $result);
    }

    public function test_it_returns_numeric_value_as_string_for_storage(): void
    {
        $result = $this->cast->set($this->model, 'test_key', 123, []);

        $this->assertSame('123', $result);
    }

    public function test_it_encodes_array_to_json_for_storage(): void
    {
        $result = $this->cast->set($this->model, 'test_key', ['foo' => 'bar'], []);

        $this->assertSame('{"foo":"bar"}', $result);
    }

    public function test_it_converts_stringable_object_for_storage(): void
    {
        $stringable = new class
        {
            public function __toString(): string
            {
                return 'stringable value';
            }
        };

        $result = $this->cast->set($this->model, 'test_key', $stringable, []);

        $this->assertSame('stringable value', $result);
    }
}
