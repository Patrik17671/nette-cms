<?php
namespace App\Model;

use Nette\Database\Context;

class ProductManager
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function addProduct($name, $url, $imageURL, $description, $sizes, $colors, $price): void
    {
        $colorsJson = json_encode($colors);
        // write to DB
        try {
            $this->database->table('products')->insert([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'price' => $price,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getProducts($location = null): array
    {
        try {
            $selection = $this->database->table('products');
            if ($location !== null) {
                $selection->where('location', $location);
            }
            return $selection->fetchAll();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //Get current product
    public function getProductById($id)
    {
        try {
            return $this->database->table('products')->get($id);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //update Product
    public function updateProduct($id, $name, $url, $imageURL, $description, $sizes, $colors, $price)
    {
        $colorsJson = json_encode($colors);
        try {
            $this->database->table('products')->where('id', $id)->update([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'price' => $price,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //delete
    public function deleteProduct($id): void
    {
        try {
            $this->database->table('products')->where('id', $id)->delete();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }
}
