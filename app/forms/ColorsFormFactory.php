<?php
namespace App\Forms;

use Nette\Application\UI\Form;

class ColorsFormFactory implements IColorsFormFactory
{
    public function create($colorsData = null): Form
    {
        $form = new Form;

        $form->addHidden('id');
        $form->addText('name', 'Name')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Please enter your name.');
        $form->addText('value', 'Value')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Please enter your value.');

        $form->addSubmit('send', $colorsData ? 'Update color' : 'Add color')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if ($colorsData) {
            $form->setDefaults([
                'id' => $colorsData->id,
                'name' => $colorsData->name,
                'value' => $colorsData->value,
            ]);
        }

        return $form;
    }
}