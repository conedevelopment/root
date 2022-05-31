<?php

namespace Cone\Root\Tests\Feature;

use Cone\Root\Conversion\GdDriver;
use Cone\Root\Interfaces\Conversion\Manager;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ConversionDriverTest extends TestCase
{
    protected $manager;

    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->app->make(Manager::class);

        $this->manager->registerConversion('thumb', function () {
            //
        });
    }

    /** @test */
    public function a_conversion_manager_has_gd_driver()
    {
        $this->assertInstanceOf(GdDriver::class, $this->manager->driver('gd'));
    }

    /** @test */
    public function a_conversion_manager_can_register_conversions()
    {
        $this->assertTrue(array_key_exists('thumb', $this->manager->getConversions()));

        $this->manager->removeConversion('thumb');

        $this->assertFalse(array_key_exists('thumb', $this->manager->getConversions()));
    }

    /** @test */
    public function a_conversion_manager_can_perform_conversions()
    {
        $medium = Medium::factory()->create([
            'name' => 'test',
            'file_name' => 'test.png',
            'mime_type' => 'image/png',
        ]);

        $image = UploadedFile::fake()->image('test.png');

        Storage::disk('public')->put(
            "{$medium->id}/test.png", File::get($image->getRealPath())
        );

        $this->manager->perform($medium);

        Storage::disk($medium->disk)->assertExists($medium->getPath());
        Storage::disk($medium->disk)->assertExists($medium->getPath('thumb'));

        $medium->delete();

        Storage::disk($medium->disk)->assertMissing($medium->id);
    }
}
