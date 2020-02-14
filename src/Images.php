<?php

namespace Admin\ResourceTools\Images;

use InWeb\Admin\App\ResourceTool;

class Images extends ResourceTool
{
    public $component = 'images-tool';

    public function name()
    {
        return __('Изображения');
    }
}
