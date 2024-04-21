<?php
    namespace App\Presenters;

    use Nette;
    use App\model\BannerManager;
    use App\Model\ProductManager;
    use App\Model\ColorsManager;

    final class ApiPresenter extends Nette\Application\UI\Presenter
    {
        private $bannerManager;
        private $productManager;
        private $colorsManager;

        public function __construct(
            BannerManager $bannerManager,
            ProductManager $productManager,
            ColorsManager $colorsManager,
        )
        {
            $this->bannerManager = $bannerManager;
            $this->productManager = $productManager;
            $this->colorsManager = $colorsManager;
        }

        public function actionBanners($id = null): void
        {
            $this->setLayout(FALSE);
            //get single banner
            if ($id !== null) {
                $banner = $this->bannerManager->getBannerById($id);
                if (!$banner) {
                    $this->sendJson(['error' => 'Banner not found']);
                    return;
                }

                $bannerData = $this->formatBannerData($banner);
                $this->sendJson($bannerData);
            }
            else {
                $location = $this->getHttpRequest()->getQuery('location');
                if (!empty($location)) {
                    $banners = $this->bannerManager->getBanners($location);
                    if (empty($banners)) {
                        $this->sendJson(['error' => 'No banners found for the specified location']);
                        return;
                    }
                } else {
                    $banners = $this->bannerManager->getBanners();
                    if (empty($banners)) {
                        $this->sendJson(['error' => 'No banners found']);
                        return;
                    }
                }

                $bannersData = array_map([$this, 'formatBannerData'], $banners);
                $bannersData = array_values($bannersData);
                $this->sendJson($bannersData);
            }
        }

        public function actionProducts($id = null): void
        {
            $this->setLayout(FALSE);
            //get single banner
            if ($id !== null) {
                $product = $this->productManager->getProductById($id);
                if (!$product) {
                    $this->sendJson(['error' => 'Product not found']);
                    return;
                }

                $productData = $this->formatBannerData($product);
                $this->sendJson($productData);
            }
            else {
                $location = $this->getHttpRequest()->getQuery('location');
                if (!empty($location)) {
                    $banners = $this->bannerManager->getBanners($location);
                    if (empty($banners)) {
                        $this->sendJson(['error' => 'No banners found for the specified location']);
                        return;
                    }
                } else {
                    $products = $this->productManager->getProducts();
                    if (empty($products)) {
                        $this->sendJson(['error' => 'No products found']);
                        return;
                    }
                }

                $productsData = array_map([$this, 'formatProductData'], $products);
                $productsData = array_values($productsData);
                $this->sendJson($productsData);
            }
        }

        private function formatBannerData($banner)
        {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'url' => $banner->url,
                'imagePath' => $banner->image_path,
                'location' => $banner->location
            ];
        }

        private function formatProductData($product)
        {
            $colorsObjects = null;
            if($product->colors){
                $colorsObjects = $this->colorsManager->formatColors($product->colors);
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => $product->url,
                'image' => $product->image,
                'description' => $product->description,
                'sizes' => $product->sizes,
                'colors' => $colorsObjects,
                'price' => $product->price
            ];
        }
    }