<?php

namespace Cone\Root\Tests\Filters;

use Cone\Root\Filters\MediaSearch;
use Cone\Root\Tests\TestCase;

class MediaSearchTest extends TestCase
{
    public function test_a_media_search_has_searchable_attributes(): void
    {
        $filter = new MediaSearch(['file_name', 'name']);

        $this->assertSame(
            ['file_name' => null, 'name' => null],
            $filter->getSearchableAttributes()
        );
    }
}
