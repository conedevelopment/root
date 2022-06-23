<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Tests\TestCase;

class ExtractTest extends TestCase
{
    protected $extract;

    public function setUp(): void
    {
        parent::setUp();

        $this->extract = new LongPosts();
    }

    /** @test */
    public function an_action_can_throw_query_resolution_exception()
    {
        $this->expectException(QueryResolutionException::class);

        $this->extract->resolveQuery($this->request);
    }
}
