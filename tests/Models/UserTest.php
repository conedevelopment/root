<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\Medium;
use Cone\Root\Models\Record;
use Cone\Root\Models\User;
use Cone\Root\Tests\TestCase;

class UserTest extends TestCase
{
    /** @test */
    public function a_user_has_uploads()
    {
        $medium = $this->admin->uploads()->save(
            Medium::factory()->make()
        );

        $this->assertTrue($this->admin->uploads->pluck('id')->contains($medium->id));
    }

    /** @test */
    public function a_user_has_records()
    {
        $record = $this->admin->records()->save(
            Record::factory()->make()->target()->associate($this->admin)
        );

        $this->assertTrue($this->admin->records->pluck('id')->contains($record->id));
    }

    /** @test */
    public function a_user_has_query_scopes()
    {
        $query = User::query()->search('test');
        $this->assertSame(
            'select * from "users" where ("users"."name" like ? or "users"."email" like ?) and "users"."deleted_at" is null',
            $query->toSql()
        );
        $this->assertSame(['%test%', '%test%'], $query->getBindings());
    }
}
