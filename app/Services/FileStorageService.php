<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class FileStorageService
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    public function storeUploadedFileAs(UploadedFile $uploadedFile, string $path, string $saveAs)
    {
        $uploadedFile->storeAs($path, $saveAs, env('FILESYSTEM_DRIVER'));
    }

    public function delete(string $filename): bool
    {
        return Storage::delete($filename);
    }

    public function saveBase64Image(string $base64Image, string $fullPath, string $fileExtension)
    {
        $image = $this->imageManager->make($base64Image);

        Storage::disk(env('FILESYSTEM_DRIVER'))->put(
            $fullPath,
            $image->stream($fileExtension)->__toString()
        );
    }
}
