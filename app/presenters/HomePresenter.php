<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Forms\BannerFormFactory;
use App\Forms\ProductFormFactory;
use App\Forms\ColorsFormFactory;
use App\Forms\CategoriesFormFactory;
use App\model\BannerManager;
use App\Model\ProductManager;
use App\Model\ColorsManager;
use App\Model\CategoriesManager;
use App\Components\CloudinaryUploader;

final class HomePresenter extends Nette\Application\UI\Presenter
{

    private $bannerFormFactory;
    private $colorsFormFactory;
    private $categoriesFormFactory;
    private $bannerManager;
    private $productManager;
    private $colorsManager;
    private $categoriesManager;
    private $cloudinaryUploader;

    private $productFormFactory;

    public function __construct(
        BannerFormFactory $bannerFormFactory,
        ProductFormFactory $productFormFactory,
        ColorsFormFactory $colorsFormFactory,
        CategoriesFormFactory $categoriesFormFactory,
        BannerManager $bannerManager,
        ProductManager $productManager,
        ColorsManager $colorsManager,
        CategoriesManager $categoriesManager,
        CloudinaryUploader $cloudinaryUploader
    )
    {
        $this->bannerFormFactory = $bannerFormFactory;
        $this->productFormFactory = $productFormFactory;
        $this->colorsFormFactory = $colorsFormFactory;
        $this->categoriesFormFactory = $categoriesFormFactory;
        $this->bannerManager = $bannerManager;
        $this->productManager = $productManager;
        $this->colorsManager = $colorsManager;
        $this->categoriesManager = $categoriesManager;
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
            $this->productManager->updateProduct($productId,$values->name, $url, $imageURL, $values->description, $values->sizes, $values->colors, $values->price, $values->categories);
        }else{
            $this->productManager->addProduct($values->name, $url, $imageURL, $values->description, $values->sizes, $values->colors, $values->price, $values->categories);
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

        $this->template->color = $color;
    }

    public function actionDeleteColor($colorId): void
    {
        if ($this->colorsManager->deleteColor($colorId)) {
            $this->flashMessage('The color has been successfully deleted.');
        } else {
            $this->flashMessage('Failed to delete color.', 'error');
        }

        $this->redirect('Home:colors');
    }

    public function renderCategories(): void
    {
        $categories = $this->categoriesManager->getCategories();
        $this->template->categories = $categories;
    }

    protected function createComponentCategoriesForm(): Form
    {
        $categoryId = $this->getParameter('categoryId');

        $categoryData = null;
        if ($categoryId) {
            $categoryData = $this->categoriesManager->getCategoryById($categoryId);
            if (!$categoryData) {
                $this->error('Category not found.');
            }
        }

        //Create form
        $form = $this->categoriesFormFactory->create($categoryData);

        //Callback from categoriesFrom
        $form->onSuccess[] = [$this, 'categoriesFormSucceeded'];

        return $form;
    }

    public function categoriesFormSucceeded(Form $form, \stdClass $values): void
    {
        $categoryId = $values->id;

        if(!empty($categoryId)){
            $this->categoriesManager->updateCategory($categoryId,$values->title,$values->url, $values->value,$values->description);
        }else{
            $this->categoriesManager->addCategory($values->title,$values->url, $values->value,$values->description);
        }

        $this->flashMessage('Operation success');
        $this->redirect('this');
    }

    public function actionEditCategory($categoryId): void
    {
        $category = $this->categoriesManager->getCategoryById($categoryId);
        if (!$category) {
            $this->error('Category not found.');
        }

        $this->template->category = $category;
    }

    public function actionDeleteCategory($categoryId): void
    {
        if ($this->categoriesManager->deleteCategory($categoryId)) {
            $this->flashMessage('The category has been successfully deleted.');
        } else {
            $this->flashMessage('Failed to delete category.', 'error');
        }

        $this->redirect('Home:categories');
    }


    protected function createComponentSidebar(): \App\Components\Sidebar\SidebarControl
    {
        return new \App\Components\Sidebar\SidebarControl();
    }
}