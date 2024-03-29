<?php
namespace App\Forms;

use Nette\Application\UI\Form;

class BannerFormFactory implements IBannerFormFactory
{
    public function create($bannerData = null): Form
    {
        $form = new Form;
        $form->addText('title', 'Title')
            ->setRequired('Please enter your title.');
        $form->addText('url', 'URL');
        $form->addUpload('image', 'Image');
        $form->addText('location', 'Location');
        $form->addSubmit('send', $bannerData ? 'Update banner' : 'Add banner');
        $form->addHidden('id');

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