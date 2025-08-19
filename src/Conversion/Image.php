<?php

declare(strict_types=1);

namespace Cone\Root\Conversion;

use Cone\Root\Models\Medium;
use Exception;
use GdImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image
{
    /**
     * The medium instance.
     */
    protected Medium $medium;

    /**
     * The path of the image.
     */
    protected string $path;

    /**
     * The file type.
     */
    protected int $type;

    /**
     * The resource.
     */
    protected GdImage $resource;

    /**
     * The attributes.
     */
    protected array $attributes = [
        'height' => 0,
        'quality' => 70,
        'width' => 0,
    ];

    /**
     * Create a new image instance.
     */
    public function __construct(Medium $medium)
    {
        $this->medium = $medium;

        $this->path = Storage::disk('local')->path('root-tmp/'.Str::random(40));

        $this->type = exif_imagetype($medium->getAbsolutePath());

        $this->create();
    }

    /**
     * Get the image path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set the width of the image.
     */
    public function setWidth(int $width): static
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    /**
     * Set the height of the image.
     */
    public function setHeight(int $height): static
    {
        $this->attributes['height'] = $height;

        return $this;
    }

    /**
     * Set the quality of the image.
     */
    public function setQuality(int $quality): static
    {
        $this->attributes['quality'] = $quality;

        return $this;
    }

    /**
     * Crop the image.
     *
     * @return $this
     */
    public function crop(?int $width = null, ?int $height = null): static
    {
        $this->resize($width, $height, true);

        return $this;
    }

    /**
     * Resize the image.
     */
    public function resize(?int $width = null, ?int $height = null, bool $crop = false): static
    {
        $x = $y = 0;
        [$originalWidth, $originalHeight] = getimagesize($this->medium->getAbsolutePath());

        $width = $width ?: $this->attributes['width'];
        $width = $width ? min($width, $originalWidth) : $originalWidth;

        $height = $height ?: $this->attributes['height'];
        $height = $height ? min($height, $originalHeight) : ($crop ? $width : $originalHeight);

        if (! $crop && $width <= $height) {
            $height = ($width / $originalWidth) * $originalHeight;
        } elseif (! $crop && $height < $width) {
            $width = ($height / $originalHeight) * $originalWidth;
        } elseif ($crop && $originalWidth < $originalHeight) {
            $y = ($originalHeight / 2) - ($originalWidth / 2);
            $originalHeight = $originalWidth;
        } elseif ($crop && $originalHeight < $originalWidth) {
            $x = ($originalWidth / 2) - ($originalHeight / 2);
            $originalWidth = $originalHeight;
        }

        $resource = imagecreatetruecolor((int) $width, (int) $height);

        if (in_array($this->type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($resource, false);
            imagesavealpha($resource, true);
            imagefill($resource, 0, 0, imagecolorallocatealpha($resource, 0, 0, 0, 127));
        }

        imagecopyresampled(
            $resource,
            $this->resource,
            0,
            0,
            (int) $x,
            (int) $y,
            (int) $width,
            (int) $height,
            $originalWidth,
            $originalHeight
        );

        imagedestroy($this->resource);
        $this->resource = $resource;
        unset($resource);

        return $this;
    }

    /**
     * Save the resource.
     */
    public function save(): void
    {
        match ($this->type) {
            IMAGETYPE_GIF => imagegif($this->resource, $this->path),
            IMAGETYPE_JPEG => imagejpeg($this->resource, $this->path, $this->attributes['quality']),
            IMAGETYPE_PNG => imagepng($this->resource, $this->path, 1),
            IMAGETYPE_WEBP => imagewebp($this->resource, $this->path, $this->attributes['quality']),
            default => throw new Exception("The file type [{$this->type}] is not supported."),
        };
    }

    /**
     * Create the resource.
     */
    protected function create(): void
    {
        $this->resource = match ($this->type) {
            IMAGETYPE_GIF => imagecreatefromgif($this->medium->getAbsolutePath()),
            IMAGETYPE_JPEG => imagecreatefromjpeg($this->medium->getAbsolutePath()),
            IMAGETYPE_PNG => imagecreatefrompng($this->medium->getAbsolutePath()),
            IMAGETYPE_WEBP => imagecreatefromwebp($this->medium->getAbsolutePath()),
            default => throw new Exception("The file type [{$this->type}] is not supported."),
        };
    }

    /**
     * Destroy the resource.
     */
    public function destroy(): void
    {
        imagedestroy($this->resource);
    }
}
