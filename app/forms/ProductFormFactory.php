<?php
namespace App\Forms;

use Nette\Application\UI\Form;
use App\Model\ColorsManager;
use App\Model\CategoriesManager;
use Tracy\Debugger;

class ProductFormFactory implements IProductFormFactory
{

    private $colorsManager;
    private $categoriesManager;

    public function __construct(ColorsManager $colorsManager, CategoriesManager $categoriesManager) {
        $this->colorsManager = $colorsManager;
        $this->categoriesManager = $categoriesManager;
    }

    public function create($ProductData = null): Form
    {
        $form = new Form;
        $colors = $this->colorsManager->getColors();
        $colorsOptions = [];
        foreach ($colors as $color) {
            $colorsOptions[$color->value] = $color->name;
        }

        $categories = $this->categoriesManager->getCategories();
        $categoriesOptions = [];
        foreach ($categories as $category) {
            $categoriesOptions[$category->value] = $category->title;
        }

        $form->addHidden('id');
        $form->addText('name', 'Name')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Please enter your title.');
        $form->addText('url', 'URL')
            ->setHtmlAttribute('class', 'form-control');
        $form->addUpload('image', 'Image')
            ->setHtmlAttribute('class', 'form-control-file');
        $form->addText('description', 'Description')
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('sizes', 'Sizes')
            ->setHtmlAttribute('class', 'form-control');
        $form->addMultiSelect('categories', 'Categories', $categoriesOptions)
            ->setHtmlAttribute('class', 'form-control selectpicker');
        $form->addMultiSelect('colors', 'Colors', $colorsOptions)
            ->setHtmlAttribute('class', 'form-control selectpicker');
        $form->addFloat('price', 'Price')
            ->setHtmlAttribute('class', 'form-control');


        $form->addSubmit('send', $ProductData ? 'Update product' : 'Add product')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if ($ProductData) {
            $colors = json_decode($ProductData->colors, true);
            if (!is_array($colors)) {
                $colors = [];
            }
            $categories = json_decode($ProductData->categories, true);
            if (!is_array($categories)) {
                $categories = [];
            }

            $form->setDefaults([
                'id' => $ProductData->id,
                'name' => $ProductData->name,
                'description' => $ProductData->description,
                'sizes' => $ProductData->sizes,
                'categories' => $categories,
                'colors' => $colors,
                'price' => $ProductData->price,
            ]);
        }

        return $form;
    }
}