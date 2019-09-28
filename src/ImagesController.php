<?php

namespace Admin\ResourceTools\Images;

use Illuminate\Support\Str;
use InWeb\Admin\App\Actions\ActionEvent;
use InWeb\Admin\App\Http\Controllers\Controller;
use InWeb\Admin\App\Http\Requests\ResourceDetailRequest;
use InWeb\Media\Image;
use InWeb\Media\WithImages;

class ImagesController extends Controller
{
    /**
     * @param ResourceDetailRequest $request
     * @return mixed
     */
    public function index(ResourceDetailRequest $request)
    {
        /** @var WithImages $model */
        $model = $request->findModelOrFail();

        return [
            'images' => $model->images
        ];
    }

    /**
     * @param ResourceDetailRequest $request
     * @return mixed
     * @throws \App\Exceptions\ModelIsNotBinded
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @todo Lock uploading when many requests are sent
     */
    public function store(ResourceDetailRequest $request)
    {
        /** @var WithImages $model */
        $model = $request->findModelOrFail();

        $images = [];

        foreach ($request->input('images') as $image) {
            $images[] = $model->images()->add($image['full_urls']['default'], true, $image['name']);
        }

        $this->actionEventForCreate($request->user(), $model, $images)->save();

        return [
            'images' => $images
        ];
    }

    /**
     * @param ResourceDetailRequest $request
     * @return mixed
     */
    public function destroy(ResourceDetailRequest $request)
    {
        /** @var WithImages $model */
        $model = $request->findModelOrFail();

        $images = [];

        $imagesForDelete = $request->input('images');

        $this->actionEventForDelete($request->user(), $model, $imagesForDelete)->save();

        foreach ($imagesForDelete as $image) {
            $model->images()->remove((int) $image);
        }
    }

    /**
     * @param Image $image
     */
    public function main(Image $image)
    {
        $image->setMain();
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model $model
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
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $images
     * @return ActionEvent
     */
    public function actionEventForDelete($user, $model, $images)
    {
        $original = $changes = [];

        /** @var \InWeb\Media\WithImages $model */
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
}
