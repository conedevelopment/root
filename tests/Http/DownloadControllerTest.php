<?php

namespace Cone\Root\Tests\Http;

use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Cone\Root\Tests\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DownloadControllerTest extends TestCase
{
    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_a_download_controller_handles_request(): void
    {
        $medium = Medium::factory()->create();

        Storage::disk('public')->put($medium->getPath(), 'test content');

        $this->actingAs($this->admin)
            ->get(URL::signedRoute('root.download', $medium))
            ->assertOk()
            ->assertDownload($medium->file_name);
    }
}
