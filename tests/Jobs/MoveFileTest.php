<?php

declare(strict_types=1);

namespace Cone\Root\Tests\Jobs;

use Cone\Root\Jobs\MoveFile;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

final class MoveFileTest extends TestCase
{
    public function test_move_file_job_can_be_dispatched(): void
    {
        Queue::fake();

        $medium = Medium::factory()->make();
        $path = '/tmp/test-file.jpg';

        MoveFile::dispatch($medium, $path, false);

        Queue::assertPushed(MoveFile::class);
    }

    public function test_move_file_job_moves_file_to_storage(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test.jpg');
        $tempPath = $file->getRealPath();

        $medium = Medium::factory()->make([
            'disk' => 'public',
            'path' => 'media/test.jpg',
        ]);

        $job = new MoveFile($medium, $tempPath, true);
        $job->handle();

        Storage::disk('public')->assertExists('media/test.jpg');
    }

    public function test_move_file_job_deletes_original_when_not_preserved(): void
    {
        Storage::fake('public');

        $tempDir = Storage::disk('local')->path('root-tmp');
        $tempPath = $tempDir.'/test-file.jpg';
        File::ensureDirectoryExists($tempDir);
        File::put($tempPath, 'test content');

        $medium = Medium::factory()->make([
            'disk' => 'public',
            'path' => 'media/test.jpg',
        ]);

        $job = new MoveFile($medium, $tempPath, false);
        $job->handle();

        Storage::disk('public')->assertExists('media/test.jpg');
        $this->assertFalse(File::exists($tempPath));
    }

    public function test_move_file_job_preserves_original_when_preserve_is_true(): void
    {
        Storage::fake('public');

        $tempDir = Storage::disk('local')->path('root-tmp');
        $tempPath = $tempDir.'/test-file.jpg';
        File::ensureDirectoryExists($tempDir);
        File::put($tempPath, 'test content');

        $medium = Medium::factory()->make([
            'disk' => 'public',
            'path' => 'media/test.jpg',
        ]);

        $job = new MoveFile($medium, $tempPath, true);
        $job->handle();

        Storage::disk('public')->assertExists('media/test.jpg');
        $this->assertTrue(File::exists($tempPath));
    }
}
