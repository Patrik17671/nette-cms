<?php
    namespace App\Presenters;

    use Nette;
    use App\model\BannerManager;
    use App\Model\ProductManager;
    use App\Model\ColorsManager;
    use App\Model\CategoriesManager;

    final class ApiPresenter extends Nette\Application\UI\Presenter
    {
        private $bannerManager;
        private $productManager;
        private $colorsManager;
        private $categoriesManager;

        public function __construct(
            BannerManager $bannerManager,
            ProductManager $productManager,
            ColorsManager $colorsManager,
            CategoriesManager $categoriesManager,
        )
        {
            $this->bannerManager = $bannerManager;
            $this->productManager = $productManager;
            $this->colorsManager = $colorsManager;
            $this->categoriesManager = $categoriesManager;
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
            $page = $this->getHttpRequest()->getQuery('page') ?: 1;
            $perPage = $this->getHttpRequest()->getQuery('perPage') ?: 10;
            $searchParams = [
                'category' => $this->getHttpRequest()->getQuery('category'),
                'sizes' => $this->getHttpRequest()->getQuery('sizes'),
                'colors' => $this->getHttpRequest()->getQuery('colors'),
            ];
            $searchParams = array_filter($searchParams, function($value) { return !empty($value); });
            //get single product
            if ($id !== null) {
                $product = $this->productManager->getProductById($id);
                if (!$product) {
                    $this->sendJson(['error' => 'Product not found']);
                    return;
                }

                $productData = $this->formatProductData($product);
                $this->sendJson($productData);
            }
            else {
                if (!empty($searchParams)) {
                    $results = $this->productManager->getProducts($searchParams, $page, $perPage);
                    if (empty($results)) {
                        $this->sendJson(['error' => 'No products found matching the criteria']);
                        return;
                    }
                } else {
                    $results = $this->productManager->getProducts($searchParams, $page, $perPage);
                    if (empty($results)) {
                        $this->sendJson(['error' => 'No products found']);
                        return;
                    }
                }

                $productsData = array_map([$this, 'formatProductData'], $results['products']);
                $productsData = array_values($productsData);
                $responseData = [
                    'products' => $productsData,
                    'page' => $results['page'],
                    'totalPages' => $results['totalPages'],
                    'perPage' => $results['perPage'],
                    'totalItems' => $results['totalItems'],
                ];
                $this->sendJson($responseData);
            }
        }

        private function formatBannerData($banner): array
        {
            return [
                'id' => $banner->id,
                'title' => $banner->title,
                'url' => $banner->url,
                'imagePath' => $banner->image_path,
                'location' => $banner->location
            ];
        }

        private function formatProductData($product): array
        {
            $colorsObjects = null;
            if($product->colors){
                $colorsObjects = $this->colorsManager->formatColors($product->colors);
            }
            $categoriesObjects = null;
            if($product->categories){
                $categoriesObjects = $this->categoriesManager->formatCategories($product->categories);
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'url' => $product->url,
                'image' => $product->image,
                'description' => $product->description,
                'sizes' => $product->sizes,
                'categories' => $categoriesObjects,
                'colors' => $colorsObjects,
                'price' => $product->price
            ];
        }
    }