<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Models\Record;
use Cone\Root\Resources\RecordResource;
use Cone\Root\Tests\TestCase;

class RecordTest extends TestCase
{
    protected $record;

    public function setUp(): void
    {
        parent::setUp();

        $this->record = $this->admin->records()->save(
            Record::factory()->make()
        );
    }

    /** @test */
    public function a_record_belongs_to_a_user()
    {
        $this->record->user()->associate($this->admin);

        $this->assertSame($this->admin->id, $this->record->user_id);
    }

    /** @test */
    public function a_record_belongs_to_a_target()
    {
        $medium = Medium::factory()->create();

        $this->record->target()->associate($medium);

        $this->assertSame(
            [Medium::class, $medium->id],
            [$this->record->target_type, $this->record->target_id]
        );
    }

    /** @test */
    public function a_record_has_resource_representation()
    {
        $this->assertInstanceOf(RecordResource::class, $this->record->toResource());
    }
}
