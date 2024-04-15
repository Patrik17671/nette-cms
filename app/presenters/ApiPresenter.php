<?php
    namespace App\Presenters;

    use Nette;

    final class ApiPresenter extends Nette\Application\UI\Presenter
    {
        private $bannerManager;

        public function __construct(\App\Model\BannerManager $bannerManager)
        {
            $this->bannerManager = $bannerManager;
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
    }