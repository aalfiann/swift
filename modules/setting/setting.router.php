<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \modules\core\helper\EtagHelper;
use \modules\session\middleware\SessionCheck;
use \modules\session\twig\SessionTwigExtension;
use \modules\user\middleware\UserAuth;


    // Setting page
    $app->get('/setting', function (Request $request, Response $response) {
        $response = $this->cache->withEtag($response, EtagHelper::updateByMinute());
        $this->view->addExtension(new SessionTwigExtension);
        return $this->view->render($response, "setting.twig", []);
    })->setName("/setting")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));