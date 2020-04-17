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

    public function multiple($value = true)
    {
        return $this->withMeta(['multiple' => $value]);
    }
}
