<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms\BannerFormFactory;
use App\model\BannerManager;
use App\Components\CloudinaryUploader;

final class HomePresenter extends Nette\Application\UI\Presenter
{

    private $bannerFormFactory;
    private $bannerManager;
    private $cloudinaryUploader;

    public function __construct(BannerFormFactory $bannerFormFactory,BannerManager $bannerManager,CloudinaryUploader $cloudinaryUploader)
    {
        $this->bannerFormFactory = $bannerFormFactory;
        $this->bannerManager = $bannerManager;
        $this->cloudinaryUploader = $cloudinaryUploader;
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    public function renderDefault(): void
    {
    }

    public function renderBanners(): void
    {
        $banners = $this->getBannersFromDatabase();
        $this->template->banners = $banners;
    }

    protected function createComponentBannerForm(): Form
    {
        $bannerId = $this->getParameter('bannerId');

        $bannerData = null;
        if ($bannerId) {
            $bannerData = $this->bannerManager->getBannerById($bannerId);
            if (!$bannerData) {
                $this->error('Banner not found.');
            }
        }

        //Create form
        $form = $this->bannerFormFactory->create($bannerData);

        //Callback from bannerFrom
        $form->onSuccess[] = [$this, 'bannerFormSucceeded'];

        return $form;
    }

    public function bannerFormSucceeded(Form $form, \stdClass $values): void
    {
        $url = $values->url ?? null;
        $location = $values->location ?? null;
        $bannerId = $values->id;

        if ($values->image->isOk() && $values->image->isImage()) {
            $imageFile = $values->image;
            $imagePath = $imageFile->getTemporaryFile();

            //upload image to cloudinary
            $imageURL = $this->cloudinaryUploader->uploadImage($imagePath);

        } else {
            $imageURL = null;
        }

        if(!empty($bannerId)){
            $this->bannerManager->updateBanner($bannerId,$values->title, $url, $imageURL, $location);
        }else{
            $this->bannerManager->addBanner($values->title, $url, $imageURL, $location);
        }

        $this->flashMessage('Operation success');
        $this->redirect('this');
    }

    private function getBannersFromDatabase()
    {
        return $this->bannerManager->getBanners();
    }

    public function actionEditBanner($bannerId): void
    {
        $banner = $this->bannerManager->getBannerById($bannerId);
        if (!$banner) {
            $this->error('Banner not found.');
        }

        $this['bannerForm']->setDefaults([
            'title' => $banner->title,
            'url' => $banner->url,
            'location' => $banner->location,
        ]);

        $this->template->banner = $banner;
    }

    public function actionDeleteBanner($bannerId): void
    {
        if ($this->bannerManager->deleteBanner($bannerId)) {
            $this->flashMessage('The banner has been successfully deleted.');
        } else {
            $this->flashMessage('Failed to delete banner.', 'error');
        }

        $this->redirect('Home:banners');
    }

    public function renderEditBanner(): void
    {
    }

    public function renderProducts(): void
    {
    }

    protected function createComponentSidebar(): \App\Components\Sidebar\SidebarControl
    {
        return new \App\Components\Sidebar\SidebarControl();
    }
}