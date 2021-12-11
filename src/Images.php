<?php

namespace Admin\ResourceTools\Images;

use InWeb\Admin\App\ResourceTool;

class Images extends ResourceTool
{
    public $component = 'images-tool';

    public function __construct()
    {
        parent::__construct();

        $this->accept('image/jpeg, image/png');
    }

    public function name(): string
    {
        return __('Изображения');
    }

    public function maxSize($value): static
    {
        return $this->withMeta(['maxSize' => $value]);
    }

    public function accept($value): static
    {
        return $this->withMeta(['accept' => $value]);
    }

    public function setThumbnail($thumbnail): static
    {
        return $this->withMeta(['thumbnail' => $thumbnail]);
    }

    public function multiple($value = true): static
    {
        return $this->withMeta(['multiple' => $value]);
    }

    public function withLanguages($value = null): static
    {
        if (! $value)
            $value = config('inweb.languages');

        return $this->withMeta(['languages' => $value]);
    }

    public function withTypes($value): static
    {
        return $this->withMeta(['types' => $value]);
    }

    public function withSvg(): static
    {
        $this->element->meta['accept'] .= ', image/svg+xml';

        return $this;
    }

    public function withGif(): static
    {
        $this->element->meta['accept'] .= ', image/gif';

        return $this;
    }
}
