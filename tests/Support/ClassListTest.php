<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Support;

use Cone\Root\Support\ClassList;
use Cone\Root\Tests\TestCase;

final class ClassListTest extends TestCase
{
    public function test_a_class_list_can_be_created_with_classes(): void
    {
        $classList = new ClassList(['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $classList->toArray());
    }

    public function test_a_class_list_can_add_classes(): void
    {
        $classList = new ClassList;

        $classList->add('foo');
        $this->assertSame(['foo'], $classList->toArray());

        $classList->add('bar baz');
        $this->assertSame(['foo', 'bar', 'baz'], $classList->toArray());

        $classList->add(['qux', 'quux']);
        $this->assertSame(['foo', 'bar', 'baz', 'qux', 'quux'], $classList->toArray());
    }

    public function test_a_class_list_removes_duplicates_when_adding(): void
    {
        $classList = new ClassList(['foo', 'bar']);

        $classList->add('foo');
        $this->assertSame(['foo', 'bar'], $classList->toArray());
    }

    public function test_a_class_list_can_remove_classes(): void
    {
        $classList = new ClassList(['foo', 'bar', 'baz']);

        $classList->remove('bar');
        $this->assertSame(['foo', 'baz'], $classList->toArray());

        $classList->remove(['foo', 'baz']);
        $this->assertSame([], $classList->toArray());
    }

    public function test_a_class_list_can_replace_classes(): void
    {
        $classList = new ClassList(['foo', 'bar', 'baz']);

        $classList->replace('bar', 'qux');
        $this->assertSame(['foo', 'qux', 'baz'], $classList->toArray());

        $classList->replace('nonexistent', 'test');
        $this->assertSame(['foo', 'qux', 'baz'], $classList->toArray());
    }

    public function test_a_class_list_can_toggle_classes(): void
    {
        $classList = new ClassList(['foo', 'bar']);

        $classList->toggle('foo');
        $this->assertSame(['bar'], $classList->toArray());

        $classList->toggle('foo');
        $this->assertSame(['bar', 'foo'], $classList->toArray());

        $classList->toggle('baz', true);
        $this->assertSame(['bar', 'foo', 'baz'], $classList->toArray());

        $classList->toggle('bar', false);
        $this->assertSame(['foo', 'baz'], $classList->toArray());
    }

    public function test_a_class_list_can_check_if_contains_class(): void
    {
        $classList = new ClassList(['foo', 'bar']);

        $this->assertTrue($classList->contains('foo'));
        $this->assertTrue($classList->contains('bar'));
        $this->assertFalse($classList->contains('baz'));
    }

    public function test_a_class_list_can_be_cleared(): void
    {
        $classList = new ClassList(['foo', 'bar', 'baz']);

        $classList->clear();
        $this->assertSame([], $classList->toArray());
    }

    public function test_a_class_list_can_be_converted_to_string(): void
    {
        $classList = new ClassList(['foo', 'bar', 'baz']);

        $this->assertSame('foo bar baz', (string) $classList);
    }
}
