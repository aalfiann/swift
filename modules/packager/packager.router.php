<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;        //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;            //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\packager\Packager as Packager;                     //Your main modules class

use \modules\session\middleware\SessionCheck;
//use \modules\session\helper\SessionHelper;
use \modules\session\twig\SessionTwigExtension;

use \modules\user\middleware\UserAuth;
use \modules\user\UserValidator as validator;
use \DavidePastore\Slim\Validation\Validation;

    // Get module information
    $app->map(['GET','OPTIONS'],'/packager/get/info', function (Request $request, Response $response) {
        $pc = new Packager();
        $body = $response->getBody();
        $body->write($pc->viewInfo());
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/packager/get/info")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // Show All Installed Packages
    $app->get('/packager/show/all', function (Request $request, Response $response) {
        $pc = new Packager();
        $body = $response->getBody();
        $body->write(json_encode($pc->showAll()));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/packager/show/all")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));


    // Install Packages from Zip (Notice: This is not safe but faster)
    $app->get('/packager/install/zip', function (Request $request, Response $response) {
        $pc = new Packager();
        $body = $response->getBody();
        $body->write(json_encode($pc->installFromZip($_GET['source'],'')));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/packager/install/zip")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // Install Packages from Zip safely
    $app->get('/packager/install/zip/safely', function (Request $request, Response $response) {
        $pc = new Packager();
        $body = $response->getBody();
        $body->write(json_encode($pc->installFromZipSafely($_GET['source'],$_GET['namespace'])));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/packager/install/zip/safely")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // Uninstall Packages
    $app->get('/packager/uninstall', function (Request $request, Response $response) {
        $pc = new Packager();
        $body = $response->getBody();
        $body->write(json_encode($pc->uninstallPackage($_GET['namespace'])));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/packager/uninstall")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // Packager Page
    $app->get('/packager', function (Request $request, Response $response) {
        $this->view->addExtension(new SessionTwigExtension);
        return $this->view->render($response, "packager.twig", []);
    })->setName("/packager")
        ->add(new UserAuth)
        ->add(new SessionCheck($container));