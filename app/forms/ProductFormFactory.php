<?php
namespace App\Forms;

use Nette\Application\UI\Form;

class ProductFormFactory implements IProductFormFactory
{
    public function create($ProductData = null): Form
    {
        $form = new Form;

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
        $form->addText('categories', 'Categories')
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('colors', 'Colors')
            ->setHtmlAttribute('class', 'form-control');
        $form->addFloat('price', 'Price')
            ->setHtmlAttribute('class', 'form-control');


        $form->addSubmit('send', $ProductData ? 'Update product' : 'Add product')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if ($ProductData) {
            $form->setDefaults([
                'id' => $ProductData->id,
                'name' => $ProductData->name,
                'description' => $ProductData->description,
                'sizes' => $ProductData->sizes,
                'categories' => $ProductData->categories,
                'colors' => $ProductData->colors,
                'price' => $ProductData->price,
            ]);
        }

        return $form;
    }
}