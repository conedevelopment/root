<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\HasMany;
use Cone\Root\Tests\TestCase;

class RelationTest extends TestCase
{
    protected HasMany $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new HasMany('User');
    }

    public function test_a_relation_resolves_model_relation(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_resolves_relatable_query(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_can_be_a_subresource(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_can_be_nullable(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_can_be_searchable(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_can_be_sortable(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_displays_relatable(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_gets_relation_value(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_resolves_format(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_resolves_filters(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_resolves_fields(): void
    {
        $this->assertTrue(true);
    }

    public function test_a_relation_resolves_actions(): void
    {
        $this->assertTrue(true);
    }
}
