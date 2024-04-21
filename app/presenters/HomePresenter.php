<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms\BannerFormFactory;
use App\Forms\ProductFormFactory;
use App\Forms\ColorsFormFactory;
use App\model\BannerManager;
use App\Model\ProductManager;
use App\Model\ColorsManager;
use App\Components\CloudinaryUploader;

final class HomePresenter extends Nette\Application\UI\Presenter
{

    private $bannerFormFactory;
    private $colorsFormFactory;
    private $bannerManager;
    private $productManager;
    private $colorsManager;
    private $cloudinaryUploader;

    private $productFormFactory;

    public function __construct(
        BannerFormFactory $bannerFormFactory,
        ProductFormFactory $productFormFactory,
        ColorsFormFactory $colorsFormFactory,
        BannerManager $bannerManager,
        ProductManager $productManager,
        ColorsManager $colorsManager,
        CloudinaryUploader $cloudinaryUploader
    )
    {
        $this->bannerFormFactory = $bannerFormFactory;
        $this->productFormFactory = $productFormFactory;
        $this->colorsFormFactory = $colorsFormFactory;
        $this->bannerManager = $bannerManager;
        $this->productManager = $productManager;
        $this->colorsManager = $colorsManager;
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
        $products = $this->productManager->getProducts();
        $this->template->products = $products;
    }

    protected function createComponentProductForm(): Form
    {
        $productId = $this->getParameter('productId');

        $productData = null;
        if ($productId) {
            $productData = $this->productManager->getProductById($productId);
            if (!$productData) {
                $this->error('Product not found.');
            }
        }

        //Create form
        $form = $this->productFormFactory->create($productData);

        //Callback from bannerFrom
        $form->onSuccess[] = [$this, 'productFormSucceeded'];

        return $form;
    }

    public function productFormSucceeded(Form $form, \stdClass $values): void
    {
        $url = $values->url ?? null;
        $productId = $values->id;

        if ($values->image->isOk() && $values->image->isImage()) {
            $imageFile = $values->image;
            $imagePath = $imageFile->getTemporaryFile();

            //upload image to cloudinary
            $imageURL = $this->cloudinaryUploader->uploadImage($imagePath);

        } else {
            $imageURL = null;
        }

        if(!empty($productId)){
            $this->productManager->updateProduct($productId,$values->name, $url, $imageURL, $values->description, $values->sizes, $values->colors, $values->price);
        }else{
            $this->productManager->addProduct($values->name, $url, $imageURL, $values->description, $values->sizes, $values->colors, $values->price);
        }

        $this->flashMessage('Operation success');
        $this->redirect('this');
    }

    public function actionEditProduct($productId): void
    {
        $product = $this->productManager->getProductById($productId);
        if (!$product) {
            $this->error('Product not found.');
        }

        $this['productForm']->setDefaults([
            'name' => $product->name,
            'url' => $product->url,
            'description' => $product->description,
            'sizes' => $product->sizes,
            'categories' => $product->categories,
            'colors' => $product->colors,
            'price' => $product->price,
        ]);

        $this->template->product = $product;
    }

    public function actionDeleteProduct($productId): void
    {
        if ($this->productManager->deleteProduct($productId)) {
            $this->flashMessage('The product has been successfully deleted.');
        } else {
            $this->flashMessage('Failed to delete product.', 'error');
        }

        $this->redirect('Home:products');
    }

    public function renderColors(): void
    {
        $colors = $this->colorsManager->getColors();
        $this->template->colors = $colors;
    }

    protected function createComponentColorsForm(): Form
    {
        $colorId = $this->getParameter('colorId');

        $colorData = null;
        if ($colorId) {
            $colorData = $this->colorsManager->getColorById($colorId);
            if (!$colorData) {
                $this->error('Color not found.');
            }
        }

        //Create form
        $form = $this->colorsFormFactory->create($colorData);

        //Callback from bannerFrom
        $form->onSuccess[] = [$this, 'colorsFormSucceeded'];

        return $form;
    }

    public function colorsFormSucceeded(Form $form, \stdClass $values): void
    {
        $colorId = $values->id;

        if(!empty($colorId)){
            $this->colorsManager->updateColor($colorId,$values->name, $values->value);
        }else{
            $this->colorsManager->addColor($values->name, $values->value);
        }

        $this->flashMessage('Operation success');
        $this->redirect('this');
    }

    public function actionEditColor($colorId): void
    {
        $color = $this->colorsManager->getColorById($colorId);
        if (!$color) {
            $this->error('Color not found.');
        }

        $this['colorsForm']->setDefaults([
            'name' => $color->name,
            'value' => $color->value,
        ]);

        $this->template->product = $color;
    }

    public function actionDeleteColor($colorId): void
    {
        if ($this->colorsManager->deleteProduct($colorId)) {
            $this->flashMessage('The color has been successfully deleted.');
        } else {
            $this->flashMessage('Failed to delete color.', 'error');
        }

        $this->redirect('Home:colors');
    }


    protected function createComponentSidebar(): \App\Components\Sidebar\SidebarControl
    {
        return new \App\Components\Sidebar\SidebarControl();
    }
}