<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        $router->addRoute('api/v1/banners[/<id>]', 'Api:banners');

        $router->addRoute('sign/in', 'Sign:in');
        $router->addRoute('banner/edit/<bannerId>', 'Home:editBanner');
        $router->addRoute('banners', 'Home:banners');
        $router->addRoute('products', 'Home:products');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
