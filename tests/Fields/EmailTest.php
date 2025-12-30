<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Fields;

use Cone\Root\Fields\Email;
use Cone\Root\Tests\TestCase;

final class EmailTest extends TestCase
{
    protected Email $field;

    protected function setUp(): void
    {
        parent::setUp();

        $this->field = new Email('Email');
    }

    public function test_an_email_field_has_email_type(): void
    {
        $this->assertSame('email', $this->field->getAttribute('type'));
    }
}
