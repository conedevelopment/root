<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Listeners;

use Cone\Root\Listeners\FormatRootStubs;
use Cone\Root\Tests\TestCase;
use Illuminate\Foundation\Events\VendorTagPublished;
use Illuminate\Support\Facades\App;

final class FormatRootStubsTest extends TestCase
{
    public function test_listener_formats_root_stubs(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'stub_');
        file_put_contents($tempFile, '<?php namespace {{ namespace }}Tests;');

        $event = new VendorTagPublished('root-stubs', [$tempFile]);

        $listener = new FormatRootStubs;
        $listener->handle($event);

        $contents = file_get_contents($tempFile);

        $this->assertStringContainsString(App::getNamespace(), $contents);
        $this->assertStringNotContainsString('{{ namespace }}', $contents);

        unlink($tempFile);
    }

    public function test_listener_ignores_non_root_stubs_tag(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'stub_');
        file_put_contents($tempFile, '<?php namespace {{ namespace }}Tests;');

        $event = new VendorTagPublished('other-tag', [$tempFile]);

        $listener = new FormatRootStubs;
        $listener->handle($event);

        $contents = file_get_contents($tempFile);

        $this->assertStringContainsString('{{ namespace }}', $contents);

        unlink($tempFile);
    }

    public function test_listener_handles_multiple_files(): void
    {
        $tempFile1 = tempnam(sys_get_temp_dir(), 'stub_');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'stub_');

        file_put_contents($tempFile1, '<?php namespace {{ namespace }}Tests;');
        file_put_contents($tempFile2, '<?php namespace {{ namespace }}Models;');

        $event = new VendorTagPublished('root-stubs', [$tempFile1, $tempFile2]);

        $listener = new FormatRootStubs;
        $listener->handle($event);

        $contents1 = file_get_contents($tempFile1);
        $contents2 = file_get_contents($tempFile2);

        $this->assertStringNotContainsString('{{ namespace }}', $contents1);
        $this->assertStringNotContainsString('{{ namespace }}', $contents2);

        unlink($tempFile1);
        unlink($tempFile2);
    }
}
