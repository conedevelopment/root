<?php

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Email;
use Cone\Root\Tests\TestCase;

class EmailTest extends TestCase
{
    protected Email $field;

    public function setUp(): void
    {
        parent::setUp();

        $this->field = new Email('Email');
    }

    public function test_an_email_field_has_email_type(): void
    {
        $this->assertSame('email', $this->field->getAttribute('type'));
    }

    public function test_an_email_field_has_email_validation_rule(): void
    {
        $rules = $this->field->getRules();

        $this->assertSame(['string', 'email'], $rules['*']);
    }
}
