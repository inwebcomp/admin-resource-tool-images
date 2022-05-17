<?php

namespace Admin\ResourceTools\Images;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use InWeb\Media\Images\Image;

class ImageResource extends JsonResource
{
    public static $wrap = false;

    public function toArray($request): array
    {
        /** @var Image $this */
        $path = $this->getStorage()->path($this->getPath());

        if (File::exists($path)) {
            [$width, $height] = getimagesize($path);

            $this->width = $width;
            $this->height = $height;
        }

        return [
            'id'           => $this->id,
            'url'          => $this->getUrl($request->input('thumbnail') ?: 'original'),
            'original_url' => $this->getUrl(),
            'language'     => $this->language,
            'main'         => $this->isMain(),
            'height'       => $this->height,
            'width'        => $this->width,
        ];
    }
}
