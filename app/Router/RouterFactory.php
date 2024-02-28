<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory {
    use Nette\StaticClass;

    public static function createRouter(): RouteList {
        $router = new RouteList;
        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');
        $router->addRoute('<presenter>[/<id>]', 'Login:default');
//        $router->addRoute('<presenter>[/<id>]', 'AddNewArticleForm:default');
        $router->addRoute('<presenter>[/<id>]', 'AddNewArticleForm:show');

        return $router;
    }
}
