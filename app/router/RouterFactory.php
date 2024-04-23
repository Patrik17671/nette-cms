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
        $router->addRoute('api/v1/products[/<id>]', 'Api:products');

        $router->addRoute('sign/in', 'Sign:in');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
		return $router;
	}
}
