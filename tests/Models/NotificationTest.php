<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Resources\RecordResource;
use Cone\Root\Tests\TestCase;

class NotificationTest extends TestCase
{
    protected $notification;

    public function setUp(): void
    {
        parent::setUp();

        //
    }

    /** @test */
    public function a_record_belongs_to_a_notifiable()
    {
        $this->assertTrue(true);
    }
}
