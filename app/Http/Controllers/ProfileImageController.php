<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageCreateRequest;
use App\Http\Transformers\ProfileImageTransformer;
use App\Models\ProfileImage;
use App\Services\FileStorageService;
use EllipseSynergie\ApiResponse\Contracts\Response;

class ProfileImageController extends Controller
{
    /**
     * @var FileStorageService
     */
    public $fileStorageService;

    /**
     * @var Response
     */
    public $response;

    public function __construct(Response $response, FileStorageService $fileStorageService)
    {
        $this->response = $response;
        $this->fileStorageService = $fileStorageService;
    }

    public function create(ProfileImageCreateRequest $request)
    {
        if ($request->user()->profileImage) {
            $this->deleteProfileImage($request);
        }


        $profileImage = ProfileImage::create([
            'user_id' => $request->user()->id,
        ]);

        $this->fileStorageService->saveBase64Image(
            $request->image,
            ProfileImage::IMAGE_UPLOAD_FOLDER . '/' . $request->user()->id . '/' . $profileImage->id . '.' . ProfileImage::IMAGE_JPG_TYPE,
            ProfileImage::IMAGE_JPG_TYPE
        );

        return $this->response->withItem(
            $profileImage,
            new ProfileImageTransformer()
        )->setStatusCode(201);
    }

    protected function deleteProfileImage(ProfileImageCreateRequest $request)
    {
        $fileName = ProfileImage::IMAGE_UPLOAD_FOLDER . '/' . $request->user()->id . '/' . $request->user()->profileImage->id . '.' . ProfileImage::IMAGE_JPG_TYPE;

        $this->fileStorageService->delete($fileName);

        $request->user()->profileImage->delete();
    }
}
