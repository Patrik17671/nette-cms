<?php
namespace App\Model;

use Nette\Database\Context;

class CategoriesManager
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function addCategory($title,$url,$value,$description): void
    {
        // write to DB
        try {
            $this->database->table('categories')->insert([
                'title' => $title,
                'url' => $url,
                'value' => $value,
                'description' => $description,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getCategories(): array
    {
        try {
            $selection = $this->database->table('categories');
            return $selection->fetchAll();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //Get current banner
    public function getCategoryById($id)
    {
        try {
            return $this->database->table('categories')->get($id);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //update banner
    public function updateCategory($id,$title,$url,$value,$description)
    {
        try {
            $this->database->table('categories')->where('id', $id)->update([
                'title' => $title,
                'url' => $url,
                'value' => $value,
                'description' => $description,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //delete
    public function deleteCategory($id): void
    {
        try {
            $this->database->table('categories')->where('id', $id)->delete();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

}
