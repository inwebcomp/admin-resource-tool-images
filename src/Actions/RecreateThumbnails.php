<?php

namespace Admin\ResourceTools\Images\Actions;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use InWeb\Admin\App\Actions\Action;
use InWeb\Admin\App\Fields\ActionFields;

class RecreateThumbnails extends Action
{
    use SerializesModels;

    public $onlyOnDetail = true;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    public function icon()
    {
        return 'far fa-sync-alt';
    }

    public function name()
    {
        return __('Переделать миниатюры');
    }

    /**
     * Perform the action on the given models.
     *
     * @param ActionFields $fields
     * @param \Illuminate\Support\Collection $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->isNotEmpty()) {
            foreach ($models as $model) {
                $model->images()->recreateThumbnails();
            }
        }

        return Action::message(__("Миниатюры переделаны"));
    }
}
