<?php

namespace Cone\Root\Tests\Conversion;

use Cone\Root\Conversion\Image;
use Cone\Root\Models\Medium;
use Cone\Root\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class ImageTest extends TestCase
{
    public function test_jpeg_can_be_converted(): void
    {
        $medium = Medium::factory()->create(['file_name' => 'test.jpg']);

        Storage::disk('public')->makeDirectory($medium->uuid);

        $i = imagecreate(800, 400);
        imagejpeg($i, $medium->getAbsolutePath());
        imagedestroy($i);

        ($image = new Image($medium))->setQuality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->resize()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->crop()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 100], [$w, $h]);
    }

    public function test_png_can_be_converted(): void
    {
        $medium = Medium::factory()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->uuid);

        $i = imagecreate(800, 400);
        imagecolorallocate($i, 0, 0, 0);
        imagesavealpha($i, true);
        imagepng($i, $medium->getAbsolutePath());
        imagedestroy($i);

        ($image = new Image($medium))->setQuality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->resize()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->crop()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 100], [$w, $h]);
    }

    public function test_gif_can_be_converted(): void
    {
        $medium = Medium::factory()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->uuid);

        $i = imagecreate(800, 400);
        imagegif($i, $medium->getAbsolutePath());
        imagedestroy($i);

        ($image = new Image($medium))->setQuality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->resize()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->crop()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 100], [$w, $h]);
    }

    public function test_webp_can_be_converted(): void
    {
        $medium = Medium::factory()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->uuid);

        $i = imagecreatetruecolor(800, 400);
        imagewebp($i, $medium->getAbsolutePath());
        imagedestroy($i);

        ($image = new Image($medium))->setQuality(70)->resize(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 200], [$w, $h]);

        ($image = new Image($medium))->resize(400, 100)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->resize()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([200, 100], [$w, $h]);

        ($image = new Image($medium))->crop(400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 400], [$w, $h]);

        ($image = new Image($medium))->crop(100, 400)->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([100, 400], [$w, $h]);

        ($image = new Image($medium))->setWidth(400)->setHeight(100)->crop()->save();
        [$w, $h] = getimagesize($image->getPath());
        $this->assertSame([400, 100], [$w, $h]);
    }

    public function test_not_supported_types_cannot_be_converted(): void
    {
        $medium = Medium::factory()->create(['file_name' => 'test.png']);

        Storage::disk('public')->makeDirectory($medium->uuid);

        $i = imagecreatetruecolor(800, 400);
        imagexbm($i, $medium->getAbsolutePath());
        imagedestroy($i);

        $type = exif_imagetype($medium->getAbsolutePath());

        $this->expectExceptionMessage("The file type [{$type}] is not supported");
        (new Image($medium))->setQuality(70)->resize(400)->save();
    }
}
