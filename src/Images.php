<?php

namespace Admin\ResourceTools\Images;

use InWeb\Admin\App\ResourceTool;

class Images extends ResourceTool
{
    public $component = 'images-tool';
    public $types = ['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'];

    public function __construct()
    {
        parent::__construct();

        $this->withMeta(['types' => $this->types]);
    }

    public function types($types)
    {
        $this->types = $types;
        return $this;
    }

    public function name()
    {
        return __('Изображения');
    }
}
