<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ImageRepository;
use App\Http\Requests\DeletePhotoRequest;
use Illuminate\Support\Facades\Validator;

class ImageController extends ApiController
{
    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @OA\Post(
     *  path="/user/profile/avatar",
     *  operationId="profileAvatar",
     *  tags={"User"},
     *  summary="Add or Change User Profile Picture",
     *  description="Upload User Profile Picture",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Upload User Profile Picture",
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"avatar"},
     *              @OA\Property(property="avatar", type="string", format="binary"),
     *          )
     *      )
     *  ),
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function create(Request $request)
    {
        try {
            $user = Auth::user();
            if ($request->hasFile('avatar')) {
                if ($user->getFirstMedia('avatar')) {
                    $user->clearMediaCollection('avatar');
                }
                $photoInstance = $user->addMediaFromRequest('avatar')->toMediaCollection('avatar', 'azure');
                return $this->sendResponse($photoInstance, "Profile Image Uploaded successfully");
            }
            return $this->sendError("file not found");
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/user/profile/photos",
     *  operationId="UploadProfilePhotos",
     *  tags={"User"},
     *  summary="Upload User Photos",
     *  description="Upload User Photos",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      description="Upload User Photos",
     *      @OA\MediaType(
     *          mediaType="multipart/form-data",
     *          @OA\Schema(
     *              required={"photos"},
     *              @OA\Property(property="photos", type="string", format="binary"),
     *          )
     *      )
     *  ),
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function store(Request $request)
    {
        $request->validate([
            'photos' => 'required|max:5000',
        ]);
        // $photo_size = $request->file('photos')->getSize();
        // $photo_size = (float)(number_format($photo_size / 1048576, 2));
        // if ($photo_size > 10) {
        //     return $this->sendError("File is too large max allowed size is 5mb");
        // }
        try {
            $user = Auth::user();

            if ($request->hasFile('photos')) {
                $photos = $user->getMedia('photos')->toArray();
                $galleryCount = count($photos);

                if ($galleryCount >= 12) {
                    return $this->sendError("Can't upload image, there is already 12 images in the gallery.");
                } else {
                    $photoInstance = $user->addMediaFromRequest('photos')->toMediaCollection('photos', 'azure');
                    return $this->sendResponse($photoInstance, "Images upload successfully");
                }
            }

            return $this->sendError("Images not found");
        } catch (Exception $exception) {
            // dd($exception->getMessage());
            return $this->sendError("File has a size is greater than the maximum allowed 5 MB");
        }
    }
    /**
     * @OA\GET(
     *  path="/user/profile/avatar",
     *  operationId="getProfileAvatar",
     *  tags={"User"},
     *  summary="Fetch User Profile Picture",
     *  description="Fetch User Profile Picture",
     *  security={{ "api_key_security": {} }},
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function show()
    {
        try {
            $user = Auth::user();
            $avatar = $user->getMedia('avatar')->first();
            return $this->sendResponse($avatar, "Success");
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\GET(
     *  path="/user/profile/photos",
     *  operationId="profilePhotos",
     *  tags={"User"},
     *  summary="Fetch User Photos",
     *  description="Fetch User Photos",
     *  security={{ "api_key_security": {} }},
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // $avatar = $user->getMedia('avatar')->toArray();
            $photos = $user->getMedia('photos')->toArray();
            // $gallery = array_merge($avatar, $photos);
            // dd(array_values($gallery));
            return $this->sendResponse($photos, "Success");
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *  path="/user/profile/photos",
     *  operationId="deletePhoto",
     *  tags={"User"},
     *  summary="Delete photo from gallery",
     *  description="Delete photo",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"file_id"},
     *          @OA\Property(property="file_id", type="integer")
     *      )
     *  ),
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function delete(DeletePhotoRequest $request)
    {
        try {
            $data = $this->imageRepository->deletePhoto($request);
            return $this->sendResponse($data, 'Photo deleted successfully');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @OA\Post(
     *  path="/user/profile/set-profile-picture",
     *  operationId="setUserProfile",
     *  tags={"User"},
     *  summary="Set user profile picture from gallery",
     *  description="Set user profile picture from gallery",
     *  security={{ "api_key_security": {} }},
     *  @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *          required={"file_id"},
     *          @OA\Property(property="file_id", type="integer")
     *      )
     *  ),
     *  @OA\Response(
     *       response=200,
     *       description="Response",
     *       @OA\JsonContent(
     *          @OA\Property(property="success", type="boolean",format="true|false"),
     *          @OA\Property(property="data", type="object"),
     *          @OA\Property(property="message", type="string"),
     *       )
     *  )
     *  )
     */

    public function setProfilePicture(Request $request)
    {
        try {
            $media = $this->imageRepository->setProfilePicture($request->all());
            return $this->sendResponse($media, 'success');
        } catch (Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
