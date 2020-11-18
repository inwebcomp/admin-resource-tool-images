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

    public function name()
    {
        return __('Изображения');
    }

    public function maxSize($value)
    {
        return $this->withMeta(['maxSize' => $value]);
    }

    public function accept($value)
    {
        return $this->withMeta(['accept' => $value]);
    }

    public function setThumbnail($thumbnail)
    {
        return $this->withMeta(['thumbnail' => $thumbnail]);
    }

    public function multiple($value = true)
    {
        return $this->withMeta(['multiple' => $value]);
    }

    public function withLanguages($value = null)
    {
        if (! $value)
            $value = config('inweb.languages');

        return $this->withMeta(['languages' => $value]);
    }

    public function withTypes($value)
    {
        return $this->withMeta(['types' => $value]);
    }
}
