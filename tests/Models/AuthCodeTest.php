<?php

namespace Cone\Root\Tests\Models;

use Cone\Root\Models\AuthCode;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;

class AuthCodeTest extends TestCase
{
    protected User $user;

    protected AuthCode $code;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->code = AuthCode::factory()->for($this->user)->create();
    }

    public function test_an_auth_code_belongs_to_a_user(): void
    {
        $this->assertTrue($this->code->user->is($this->user));
    }

    public function test_an_auth_code_can_be_expired(): void
    {
        $this->assertTrue($this->code->active());
        $this->assertFalse($this->code->expired());

        $expired = AuthCode::factory()->for($this->user)->expired()->create();

        $this->assertFalse($expired->active());
        $this->assertTrue($expired->expired());
    }

    public function test_an_auth_code_has_active_query_scope(): void
    {
        $this->assertSame(
            'select * from "root_auth_codes" where "root_auth_codes"."expires_at" > ?',
            AuthCode::query()->active()->toSql()
        );
    }

    public function test_an_auth_code_has_expired_query_scope(): void
    {
        $this->assertSame(
            'select * from "root_auth_codes" where "root_auth_codes"."expires_at" <= ?',
            AuthCode::query()->expired()->toSql()
        );
    }
}
