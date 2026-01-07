<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Exceptions;

use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Tests\TestCase;
use Exception;

final class ExceptionsTest extends TestCase
{
    public function test_query_resolution_exception_can_be_thrown(): void
    {
        $this->expectException(QueryResolutionException::class);
        $this->expectExceptionMessage('Test message');

        throw new QueryResolutionException('Test message');
    }

    public function test_query_resolution_exception_extends_exception(): void
    {
        $exception = new QueryResolutionException('Test');

        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function test_resource_resolution_exception_can_be_thrown(): void
    {
        $this->expectException(ResourceResolutionException::class);
        $this->expectExceptionMessage('Test message');

        throw new ResourceResolutionException('Test message');
    }

    public function test_resource_resolution_exception_extends_exception(): void
    {
        $exception = new ResourceResolutionException('Test');

        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function test_save_form_data_exception_can_be_thrown(): void
    {
        $this->expectException(SaveFormDataException::class);
        $this->expectExceptionMessage('Test message');

        throw new SaveFormDataException('Test message');
    }

    public function test_save_form_data_exception_extends_exception(): void
    {
        $exception = new SaveFormDataException('Test');

        $this->assertInstanceOf(Exception::class, $exception);
    }
}
