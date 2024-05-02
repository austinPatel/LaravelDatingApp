<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageRepository
{
    public function deletePhoto($request)
    {
        $media = Media::find($request->input('file_id'));
        $model = User::find($media->model_id);
        $model->deleteMedia($media->id);
        return $media;

        // echo "File _id-" . $request->file_id;
        // exit;
        // $user_id = Auth::user()->id;
        // $media = Media::where(['model_id' => $user_id, 'id' => $request->file_id])->delete();
        // return $media;
    }
    public function setProfilePicture(array $data)
    {
        $media = Media::find($data['file_id']);
        $user = Auth::user();
        $userMedia = $user->getFirstMedia('avatar');
        if ($userMedia) {
            $userMedia->collection_name = "photos";
            $userMedia->save();
        }
        $media->collection_name = "avatar";
        $model = User::find($media->model_id);
        $media->save();
        return $media;
    }
}
