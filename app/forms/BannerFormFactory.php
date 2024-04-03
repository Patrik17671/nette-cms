<?php
namespace App\Forms;

use Nette\Application\UI\Form;

class BannerFormFactory implements IBannerFormFactory
{
    public function create($bannerData = null): Form
    {
        $form = new Form;

        $form->addText('title', 'Title')
            ->setHtmlAttribute('class', 'form-control')
            ->setRequired('Please enter your title.');
        $form->addText('url', 'URL')
            ->setHtmlAttribute('class', 'form-control');
        $form->addUpload('image', 'Image')
            ->setHtmlAttribute('class', 'form-control-file');
        $form->addText('location', 'Location')
            ->setHtmlAttribute('class', 'form-control');

        $form->addSubmit('send', $bannerData ? 'Update banner' : 'Add banner')
            ->setHtmlAttribute('class', 'btn btn-primary');

        if ($bannerData) {
            $form->setDefaults([
                'id' => $bannerData->id,
                'title' => $bannerData->title,
                'url' => $bannerData->url,
                'location' => $bannerData->location,
            ]);
        }

        return $form;
    }
}