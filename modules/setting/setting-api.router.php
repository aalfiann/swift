<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \modules\session\middleware\SessionCheck;
use \modules\user\middleware\UserAuth;

use \modules\setting\SettingManager;
use \modules\setting\SettingValidator as validator;
use \DavidePastore\Slim\Validation\Validation;

    // API save App setting
    $app->post('/setting/app/api/json', function (Request $request, Response $response) {
        $arr = [];
        if($request->getAttribute('has_errors')){
            $errors = $request->getAttribute('errors');
            $data = [
                'status' => 'error',
                'message' => 'Parameter is not valid! ',
                'problem' => $errors
            ];
            $arr = $data;
        } else {
            $sm = new SettingManager();
            $sm->name = $request->getParam('name');
            $sm->language = $request->getParam('language');
            $sm->http_max_age = $request->getParam('http_max_age');
            $sm->timezone = $request->getParam('timezone');
            $sm->template_handler = $request->getParam('template_handler');
            $sm->template_folder = $request->getParam('template_folder');
            $sm->log_level = $request->getParam('log_level');
            $arr = $sm->saveApp();
        }
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/app/api/json')
        ->add(new Validation(validator::app()))
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API save Smtp setting
    $app->post('/setting/smtp/api/json', function (Request $request, Response $response) {
        $arr = [];
        if($request->getAttribute('has_errors')){
            $errors = $request->getAttribute('errors');
            $data = [
                'status' => 'error',
                'message' => 'Parameter is not valid! ',
                'problem' => $errors
            ];
            $arr = $data;
        } else {
            $sm = new SettingManager();
            $sm->host = $request->getParam('host');
            $sm->autotls = $request->getParam('autotls');
            $sm->auth = $request->getParam('auth');
            $sm->secure = $request->getParam('secure');
            $sm->port = $request->getParam('port');
            $sm->defaultnamefrom = $request->getParam('defaultnamefrom');
            $sm->username = $request->getParam('username');
            $sm->password = $request->getParam('password');
            $sm->debug = $request->getParam('debug');
            $arr = $sm->saveSmtp();
        }
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/smtp/api/json')
        ->add(new Validation(validator::smtp()))
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API save Template variable
    $app->post('/setting/template/api/json', function (Request $request, Response $response) {
        $arr = [];
        if($request->getAttribute('has_errors')){
            $errors = $request->getAttribute('errors');
            $data = [
                'status' => 'error',
                'message' => 'Parameter is not valid! ',
                'problem' => $errors
            ];
            $arr = $data;
        } else {
            $sm = new SettingManager();
            $sm->data = json_decode($request->getParam('json'),true);
            $arr = $sm->saveTemplate();
        }
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/template/api/json')
        ->add(new Validation(validator::template()))
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API clear Template variable
    $app->post('/setting/template/clear/api/json', function (Request $request, Response $response) {
        $sm = new SettingManager();
        $arr = $sm->clearTemplate();
        
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/template/clear/api/json')
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API load App setting
    $app->get('/setting/app/load/api/json', function (Request $request, Response $response) {
        $sm = new SettingManager();
        $arr = $sm->load('app.json');
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/app/load/api/json')
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API load Smtp setting
    $app->get('/setting/smtp/load/api/json', function (Request $request, Response $response) {
        $sm = new SettingManager();
        $arr = $sm->load('smtp.json');
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/smtp/load/api/json')
        ->add(new UserAuth)
        ->add(new SessionCheck($container));

    // API load Template setting
    $app->get('/setting/template/load/api/json', function (Request $request, Response $response) {
        $sm = new SettingManager();
        $arr = $sm->load('template.json');
        return $response->withStatus(200)
            ->withHeader('Content-Type','application/json; charset=utf-8')
            ->withJSON($arr);
    })->setName('/setting/template/load/api/json')
        ->add(new UserAuth)
        ->add(new SessionCheck($container));