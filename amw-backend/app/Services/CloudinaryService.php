<?php

namespace App\Services;

use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryService
{
    private UploadApi $uploadApi;

    public function __construct()
    {
        $cloudinaryUrl = config('services.cloudinary.url');

        if (empty($cloudinaryUrl)) {
            throw new RuntimeException('La variable CLOUDINARY_URL no está configurada.');
        }

        $separator = str_contains($cloudinaryUrl, '?') ? '&' : '?';

        Configuration::instance($cloudinaryUrl . $separator . 'secure=true');

        $this->uploadApi = new UploadApi();
    }

    /**
     * Sube una imagen a Cloudinary dentro de una carpeta de AMW.
     */
    public function uploadImage(UploadedFile $image, string $folder): array
    {
        $result = $this->uploadApi->upload(
            $image->getRealPath(),
            [
                'folder' => 'amw/' . $folder,
                'resource_type' => 'image',
                'unique_filename' => true,
                'overwrite' => false,
            ]
        );

        return [
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
        ];
    }

    /**
     * Elimina una imagen de Cloudinary y solicita invalidar su copia en CDN.
     */
    public function deleteImage(?string $publicId): void
    {
        if (empty($publicId)) {
            return;
        }

        $this->uploadApi->destroy(
            $publicId,
            [
                'resource_type' => 'image',
                'invalidate' => true,
            ]
        );
    }
}
