<?php
namespace App\Model;

use Nette\Database\Context;

class BannerManager
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function addBanner($title, $url, $imageURL, $location): void
    {
        // write to DB
        try {
            $this->database->table('banners')->insert([
                'title' => $title,
                'url' => $url,
                'image_path' => $imageURL,
                'location' => $location
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getBanners($location = null): array
    {
        try {
            $selection = $this->database->table('banners');
            if ($location !== null) {
                $selection->where('location', $location);
            }
            return $selection->fetchAll();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //Get current banner
    public function getBannerById($id)
    {
        try {
            return $this->database->table('banners')->get($id);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //update banner
    public function updateBanner($id, $title, $url, $imageURL, $location)
    {
        try {
            $this->database->table('banners')->where('id', $id)->update([
                'title' => $title,
                'url' => $url,
                'image_path' => $imageURL,
                'location' => $location
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //delete
    public function deleteBanner($id): void
    {
        try {
            $this->database->table('banners')->where('id', $id)->delete();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
