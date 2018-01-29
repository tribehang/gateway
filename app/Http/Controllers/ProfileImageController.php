<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageCreateRequest;
use App\Http\Transformers\ProfileImageTransformer;
use App\Models\ProfileImage;
use App\Repositories\ProfileImageRepository;
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

    /*
     * @var ProfileImageRepository
     */
    public $modelRepository;

    public function __construct(Response $response, FileStorageService $fileStorageService, ProfileImageRepository $modelRepository)
    {
        $this->response = $response;
        $this->fileStorageService = $fileStorageService;
        $this->modelRepository = $modelRepository;
    }

    public function create(ProfileImageCreateRequest $request)
    {
        if ($request->user()->profileImage) {
            $this->deleteProfileImage($request);
        }

        $profileImage = $this->modelRepository->create([
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
