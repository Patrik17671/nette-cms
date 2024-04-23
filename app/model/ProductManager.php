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

    public function addProduct($name, $url, $imageURL, $description, $sizes, $colors, $price, $categories): void
    {
        $colorsJson = json_encode($colors);
        $categoriesJson = json_encode($categories);
        // write to DB
        try {
            $this->database->table('products')->insert([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'categories' => $categoriesJson,
                'price' => $price,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getProducts($params = null): array
    {
        try {
            $selection = $this->database->table('products');

            if (!empty($params['category'])) {
                $categoryJson = json_encode([$params['category']]);
                $selection->where('json_contains(categories, ?)', $categoryJson);
            }

            if (!empty($params['colors'])) {
                $colorsJson = json_encode([$params['colors']]);
                $selection->where('json_contains(colors, ?)', $colorsJson);
            }

            if (!empty($params['sizes'])) {
                $sizes = explode(',', $params['sizes']);
                foreach ($sizes as $size) {
                    $selection->where('sizes LIKE ?', "%$size%");
                }
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
    public function updateProduct($id, $name, $url, $imageURL, $description, $sizes, $colors, $price, $categories)
    {
        $colorsJson = json_encode($colors);
        $categoriesJson = json_encode($categories);
        try {
            $this->database->table('products')->where('id', $id)->update([
                'name' => $name,
                'url' => $url,
                'image' => $imageURL,
                'description' => $description,
                'sizes' => $sizes,
                'colors' => $colorsJson,
                'categories' => $categoriesJson,
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
