<?php
namespace App\Model;

use Nette\Database\Context;

class ColorsManager
{
    private $database;

    public function __construct(Context $database)
    {
        $this->database = $database;
    }

    public function addColor($name, $value): void
    {
        // write to DB
        try {
            $this->database->table('colors')->insert([
                'name' => $name,
                'value' => $value,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function getColors(): array
    {
        try {
            $selection = $this->database->table('colors');
            return $selection->fetchAll();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //Get current banner
    public function getColorById($id)
    {
        try {
            return $this->database->table('colors')->get($id);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //update banner
    public function updateColor($id, $name, $value)
    {
        try {
            $this->database->table('colors')->where('id', $id)->update([
                'name' => $name,
                'value' => $value,
            ]);
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    //delete
    public function deleteColors($id): void
    {
        try {
            $this->database->table('colors')->where('id', $id)->delete();
        } catch (\Nette\Database\DriverException $e) {
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    public function formatColors($selectedColorValues): array {
        $colors = $this->getColors();
        $formattedColors = [];
        $selectedColorValuesArray = is_string($selectedColorValues) ? json_decode($selectedColorValues, true) : $selectedColorValues;

        foreach ($colors as $color) {
            if (in_array($color->value, $selectedColorValuesArray)) {
                $formattedColors[] = [
                    'id' => $color->id,
                    'name' => $color->name,
                    'value' => $color->value,
                ];
            }
        }

        return $formattedColors;
    }
}
