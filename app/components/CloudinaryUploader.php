<?php
namespace App\Components;

use Nette;
use Cloudinary\Cloudinary;

class CloudinaryUploader extends Nette\Application\UI\Control
{
    private $cloudinary;

    public function __construct(string $cloudName, string $apiKey, string $apiSecret)
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true // HTTPS
            ]
        ]);
    }

    public function uploadImage($imagePath)
    {
        // load images to Cloudinary
        $result = $this->cloudinary->uploadApi()->upload($imagePath, [
            'folder' => 'nette-cms',
        ]);

        return $result['secure_url']; // return url of image
    }
}
