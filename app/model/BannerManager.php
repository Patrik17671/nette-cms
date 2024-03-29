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
        $this->database->table('banners')->insert([
            'title' => $title,
            'url' => $url,
            'image_path' => $imageURL,
            'location' => $location
        ]);
    }

    public function getBanners(): array
    {
        // load from DB
        return $this->database->table('banners')->fetchAll();
    }

    //Get current banner
    public function getBannerById($id)
    {
        return $this->database->table('banners')->get($id);
    }

    //update banner
    public function updateBanner($id, $title, $url, $imageURL, $location)
    {
        $this->database->table('banners')->where('id', $id)->update([
            'title' => $title,
            'url' => $url,
            'image_path' => $imageURL,
            'location' => $location
        ]);
    }

    //delete
    public function deleteBanner($id): void
    {
        $this->database->table('banners')->where('id', $id)->delete();
    }
}
