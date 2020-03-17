<?php

namespace Admin\ResourceTools\Images;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InWeb\Admin\App\Actions\ActionEvent;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\AdminRequest;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Base\Contracts\Cacheable;
use InWeb\Base\Entity;
use InWeb\Media\Images\Image;
use InWeb\Media\Images\WithImages;

class ImagesController extends Controller
{
    /**
     * @param ResourceDetailRequest $request
     * @return mixed
     */
    public function index(ResourceDetailRequest $request)
    {
        /** @var WithImages|Entity $model */
        $model = $request->findModelOrFail();

        return [
            'images' => $model->images
        ];
    }

    /**
     * @param ResourceDetailRequest $request
     * @return mixed
     * @throws FileNotFoundException
     * @todo Lock uploading when many requests are sent
     */
    public function store(ResourceDetailRequest $request)
    {
        /** @var WithImages|Entity $model */
        $model = $request->findModelOrFail();

        $images = [];

        $inputImages = $request->input('images');

        if ($inputImages) {
            foreach ($inputImages as $image) {
                $images[] = $model->images()->add($image['full_urls']['default'], true, $image['name']);
            }
        } else if ($url = $request->input('url')) {
            $images[] = $model->images()->add($url, true);
        }

        $this->actionEventForCreate($request->user(), $model, $images)->save();

        return [
            'images' => $images
        ];
    }

    /**
     * @param ResourceDetailRequest $request
     */
    public function destroy(ResourceDetailRequest $request)
    {
        /** @var WithImages|Entity $model */
        $model = $request->findModelOrFail();

        $imagesForDelete = $request->input('images');

        $this->actionEventForDelete($request->user(), $model, $imagesForDelete)->save();

        foreach ($imagesForDelete as $image) {
            $model->images()->remove((int) $image);
        }
    }

    /**
     * @param Image $image
     * @throws Exception
     */
    public function main(Image $image)
    {
        $image->setMain();
    }

    /**
     * @param Authenticatable $user
     * @param Model $model
     * @param Image[] $images
     * @return ActionEvent
     */
    public function actionEventForCreate($user, $model, $images)
    {
        $original = $changes = [];

        foreach ($images as $image) {
            $original['Image ' . $image->id] = '';
            $changes['Image ' . $image->id] = $image->filename;
        }

        return new ActionEvent([
            'batch_id'        => (string) Str::orderedUuid(),
            'user_id'         => $user->getAuthIdentifier(),
            'name'            => 'Image create',
            'actionable_type' => $model->getMorphClass(),
            'actionable_id'   => $model->getKey(),
            'target_type'     => $model->getMorphClass(),
            'target_id'       => $model->getKey(),
            'model_type'      => $model->getMorphClass(),
            'model_id'        => $model->getKey(),
            'fields'          => '',
            'original'        => $original,
            'changes'         => $changes,
            'status'          => 'finished',
            'exception'       => '',
        ]);
    }

    /**
     * @param Authenticatable $user
     * @param Model $model
     * @param array $images
     * @return ActionEvent
     */
    public function actionEventForDelete($user, $model, $images)
    {
        $original = $changes = [];

        /** @var WithImages $model */
        foreach ($images as $image) {
            $image = $model->getImage((int) $image);

            if (! $image)
                continue;

            $original['Image ' . $image->id] = $image->filename;
            $changes['Image ' . $image->id] = '';
        }

        return new ActionEvent([
            'batch_id'        => (string) Str::orderedUuid(),
            'user_id'         => $user->getAuthIdentifier(),
            'name'            => 'Image delete',
            'actionable_type' => $model->getMorphClass(),
            'actionable_id'   => $model->getKey(),
            'target_type'     => $model->getMorphClass(),
            'target_id'       => $model->getKey(),
            'model_type'      => $model->getMorphClass(),
            'model_id'        => $model->getKey(),
            'fields'          => '',
            'original'        => $original,
            'changes'         => $changes,
            'status'          => 'finished',
            'exception'       => '',
        ]);
    }

    public function changePositions(AdminRequest $request)
    {
        $model = $request->findModelOrFail();

        if ($model instanceof Cacheable)
            $model::clearCache($model);

        Image::updatePositionsById($request->input('images'));
    }
}
