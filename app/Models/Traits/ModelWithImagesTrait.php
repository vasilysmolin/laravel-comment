<?php

namespace Beauty\Modules\Common\Models\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait ModelWithImagesTrait
{
    protected bool $withResponsive = true;
    private string $newFiles = 'newFiles';
    private string $disk = 'goods';
    private string $collectionName = 'images';

    public array $items = [];

    public function setImages(string $images, ?string $collectionName = 'images'): self
    {
        $this->items = json_decode($images, true);
        if (!empty($collectionName)) {
            $this->collectionName = $collectionName;
        }
        return $this;
    }

    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    public function hasImages(): bool
    {
        return $this->photos->count() > 0;
    }

    public function syncImages(): void
    {
        $storage = Storage::disk($this->disk);
        $filesToCreate = $this->items['newFiles'] ?? [];
        collect($filesToCreate)->each(function ($item) use ($storage) {
            $fileContent = $item['file'];
            $crop = null;
            $rotate = null;
            $sort = null;
            $cropArray = null;
            if (!empty($item['metadata']) && !empty($item['metadata']['crop'])) {
                if (is_array($item['metadata']['crop'])) {
                    $crop = json_encode($item['metadata']['crop']);
                    $cropArray = $item['metadata']['crop'];
                }
                $sort = $item['metadata']['sort'];
                $rotate = $item['metadata']['rotate'];
            }
            $name = $this->generateFileName();
            $pathDir = $this->getOptimizeDirectory($name);
            if (preg_match('/^data:image\/(\w+);base64,/', $fileContent)) {
                $mimeType = mime_content_type($fileContent);
                $data = base64_decode(substr($fileContent, strpos($fileContent, ',') + 1));
                $extension = explode('/', $mimeType)[1];
                $storage->put("{$pathDir}.{$extension}", $data);
            } else {
                $storage->put("{$name}.jpg", base64_decode($fileContent));
                $mimeType = mime_content_type($storage->path("{$name}.jpg"));
                $storage->delete("{$name}.jpg");
                $extension = explode('/', $mimeType)[1];
                $data = base64_decode($fileContent);
                $storage->put("{$pathDir}.{$extension}", $data);
            }
            $fileContent = $storage->get("{$pathDir}.{$extension}");
            $size = $storage->size("{$pathDir}.{$extension}");

            if (!empty($rotate)) {
                $image = Image::make($fileContent);
                $resultImage = $image->rotate($rotate)->encode($extension, 100);
                $storage->put(
                    "{$pathDir}.{$extension}",
                    $resultImage
                );
                $fileContent = $storage->get("{$pathDir}.{$extension}");
            }
            collect($this->cropImages())->each(function ($resolution) use ($storage, $extension, $pathDir, $fileContent, $cropArray) {
                $image = Image::make($fileContent);
                if (!empty($cropArray)) {
                    $filteredImage = $image->crop(
                        (int) $cropArray['width'],
                        (int) $cropArray['height'],
                        (int) $cropArray['x'],
                        (int) $cropArray['y']
                    );
                    $image = $filteredImage;
                }
                $resultImage = $image->fit($resolution['width'], $resolution['height'])
                    ->encode($extension, 100);
                $storage->put(
                    "{$pathDir}_{$resolution['width']}x{$resolution['height']}.$extension",
                    $resultImage
                );
            });
            $disk = $this->disk;
            $collectionName = $this->collectionName;
            $data = compact(['name', 'extension', 'mimeType', 'size', 'disk', 'crop', 'sort', 'collectionName']);
            $this->photos()->create($data);
        });

        $filesToDelete = $this->items['deleteFiles'] ?? [];
        collect($filesToDelete)->each(function ($item) use ($storage) {
            $modelImage = \Beauty\Modules\Common\Models\Image::where('id', $item)->first();
            if (empty($modelImage)) {
                return;
            }
            $pathDir = $this->getOptimizeDirectory($modelImage->name);
            $storage->delete("{$pathDir}.{$modelImage->extension}");
            collect($this->cropImages())->each(function ($resolution) use ($storage, $pathDir, $modelImage) {
                $storage->delete("{$pathDir}_{$resolution['width']}x{$resolution['height']}.$modelImage->extension");
            });
            $modelImage->delete();
        });

        $filesToUpdate = $this->items['updateFiles'] ?? [];
        collect($filesToUpdate)->each(function ($item) use ($storage) {
            $modelImage = \Beauty\Modules\Common\Models\Image::where('id', $item['id'])->first();
            if (empty($modelImage)) {
                return;
            }
            $pathDir = $this->getOptimizeDirectory($modelImage->name);
            $crop = $item['metadata']['crop'];
            $sort = $item['metadata']['sort'];
            $rotate = $item['metadata']['rotate'];
            if (!$storage->exists("{$pathDir}.{$modelImage->extension}")) {
                return;
            }
            $fileContent = $storage->get("{$pathDir}.{$modelImage->extension}");
            if (!empty($rotate)) {
                $image = Image::make($fileContent);
                $resultImage = $image->rotate($rotate)->encode($modelImage->extension, 100);
                $storage->put(
                    "{$pathDir}.{$modelImage->extension}",
                    $resultImage
                );
                $fileContent = $storage->get("{$pathDir}.{$modelImage->extension}");
            }
            collect($this->cropImages())->each(function ($resolution) use ($storage, $crop, $pathDir, $fileContent, $modelImage) {
                $image = Image::make($fileContent);
                if (!empty($crop)) {
                    $filteredImage = $image->crop(
                        (int) $crop['width'],
                        (int) $crop['height'],
                        (int) $crop['x'],
                        (int) $crop['y']
                    );
                    $image = $filteredImage;
                }
                $resultImage = $image->fit($resolution['width'], $resolution['height'])
                    ->encode($modelImage->extension, 100);
                $storage->put(
                    "{$pathDir}_{$resolution['width']}x{$resolution['height']}.$modelImage->extension",
                    $resultImage
                );
            });
            $modelImage->crop = $crop;
            $modelImage->sort = $sort;
            $modelImage->disk = $this->disk;
            $modelImage->collectionName = $this->collectionName;
            $modelImage->update();
        });
    }

    protected function generateFileName(): string
    {
        return md5(microtime() . random_int(0, 10000000)) . random_int(0, 10000000);
    }

    protected function getOptimizeDirectory($string): string
    {
        $firstDir = mb_substr($string, 0, 2, 'utf-8');
        $secondDir = mb_substr($string, 2, 2, 'utf-8');
        return "{$this->baseFileDir()}/{$firstDir}/{$secondDir}/$string/$string";
    }

    public function getImages(?string $collectionName = 'images'): Collection
    {
        $photos = $this->photos()->where('collectionName', $collectionName)->orderBy('sort')->get();
        $storage = Storage::disk($this->disk);
        return $photos->map(function ($photo) use ($storage) {
            return $this->generatePhotoVariants($photo, $storage);
        });
    }

    public function getFirstImage(string $collectionName = 'images'): ?array
    {
        $photo = $this->photos()->where('collectionName', $collectionName)->orderBy('sort')->first();
        if (isset($photo)) {
            $storage = Storage::disk($this->disk);
            return $this->generatePhotoVariants($photo, $storage);
        } else {
            return null;
        }
    }

    private function generatePhotoVariants($photo, $storage): array
    {
        $pathDir = $this->getOptimizeDirectory($photo->name);
        $result = [];
        $result['id'] = $photo->getKey();
        foreach ($this::$CROP_IMAGES as $key => $resolution) {
            $hash = Str::random(8);
            $result[$key] = $storage->url("{$pathDir}_{$resolution['width']}x{$resolution['height']}.{$photo->extension}") . "?hash=" . $hash;
        }
        $result['original'] = $storage->url("{$pathDir}.{$photo->extension}");
        $result['crop'] = $photo->crop;
        $result['sort'] = $photo->sort;
        return $result;
    }

}
