<?php
namespace App\Forms;

use Nette\Application\UI\Form;

class CategoriesFormFactory implements ICategoriesFormFactory
{
    public function create($categoryData = null): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('title', 'Title')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Please enter your title.');
        $form->addText('url', 'URL')
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('value', 'Value')
            ->setHtmlAttribute('class', 'form-control');
        $form->addTextArea('description', 'Description')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $categoryData ? 'Update banner' : 'Add banner')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if ($categoryData) {
            $form->setDefaults([
                'id' => $categoryData->id,
                'title' => $categoryData->title,
                'url' => $categoryData->url,
                'value' => $categoryData->value,
                'description' => $categoryData->description,
            ]);
        }

        return $form;
    }
}