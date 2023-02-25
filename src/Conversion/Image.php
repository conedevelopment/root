<?php

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
     *
     * @var string
     */
    protected $path;

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
     *
     * @return void
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
     *
     * @return $this
     */
    public function setWidth(int $width): Image
    {
        $this->attributes['width'] = $width;

        return $this;
    }

    /**
     * Set the height of the image.
     *
     * @return $this
     */
    public function setHeight(int $height): Image
    {
        $this->attributes['height'] = $height;

        return $this;
    }

    /**
     * Set the quality of the image.
     *
     * @return $this
     */
    public function setQuality(int $quality): Image
    {
        $this->attributes['quality'] = $quality;

        return $this;
    }

    /**
     * Crop the image.
     *
     * @return $this
     */
    public function crop(?int $width = null, ?int $height = null): Image
    {
        $this->resize($width, $height, true);

        return $this;
    }

    /**
     * Resize the image.
     *
     * @return $this
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

        $resource = imagecreatetruecolor($width, $height);

        if (in_array($this->type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            imagealphablending($resource, false);
            imagesavealpha($resource, true);
            imagefill($resource, 0, 0, imagecolorallocatealpha($resource, 0, 0, 0, 127));
        }

        imagecopyresampled($resource, $this->resource, 0, 0, $x, $y, $width, $height, $originalWidth, $originalHeight);

        imagedestroy($this->resource);
        $this->resource = $resource;
        unset($resource);

        return $this;
    }

    /**
     * Save the resource.
     *
     *
     * @throws \Exception
     */
    public function save(): void
    {
        switch ($this->type) {
            case IMAGETYPE_GIF:
                imagegif($this->resource, $this->path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($this->resource, $this->path, $this->attributes['quality']);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->resource, $this->path, 1);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($this->resource, $this->path, $this->attributes['quality']);
                break;
            default:
                throw new Exception("The file type [{$this->type}] is not supported.");
        }
    }

     /**
      * Create the resource.
      *
      *
      * @throws \Exception
      */
     protected function create(): void
     {
         switch ($this->type) {
             case IMAGETYPE_GIF:
                 $this->resource = imagecreatefromgif($this->medium->getAbsolutePath());
                 break;
             case IMAGETYPE_JPEG:
                 $this->resource = imagecreatefromjpeg($this->medium->getAbsolutePath());
                 break;
             case IMAGETYPE_PNG:
                 $this->resource = imagecreatefrompng($this->medium->getAbsolutePath());
                 break;
             case IMAGETYPE_WEBP:
                 $this->resource = imagecreatefromwebp($this->medium->getAbsolutePath());
                 break;
             default:
                 throw new Exception("The file type [{$this->type}] is not supported.");
         }
     }

    /**
     * Destroy the resource.
     */
    public function destroy(): void
    {
        imagedestroy($this->resource);
    }
}
