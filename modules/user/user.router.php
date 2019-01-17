<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \modules\session\middleware\SessionCheck;
use \modules\session\helper\SessionHelper;
use \modules\core\helper\EtagHelper;
use \modules\user\User;
use \modules\mailer\Mailer;
use \modules\user\UserValidator as validator;
use \DavidePastore\Slim\Validation\Validation;

    // Get Login page
    $app->get('/login', function (Request $request, Response $response) {
        $body = $response->getBody();
        return $this->view->render($response, "login.twig", []);
    })->setName("/login")->add($container->get('csrf'));

    // Post Login page
    $app->post('/login', function (Request $request, Response $response) {
        $body = $response->getBody();
        $datapost = $request->getParsedBody();
        $user = new User();
        $user->username = $datapost['username'];
        $user->password = $datapost['password'];
        $data = $user->login();
        if($data['status'] == 'success') {
            $sh = new SessionHelper();
            $sh->set('username',$user->username);
            $url = $request->getUri()->withPath($this->router->pathFor('/dashboard'));
            return $response->withRedirect($url);
        }

        return $this->view->render($response, "login.twig", $data);
    })->add($container->get('csrf'));

    // Get Register page
    $app->get('/register', function (Request $request, Response $response) {
        $body = $response->getBody();
        $data = [
            'status' => '',
            'message' => ''
        ];
        return $this->view->render($response, "register.twig", $data);
    })->setName("/register")->add($container->get('csrf'));

    // Post Register page
    $app->post('/register', function (Request $request, Response $response) {
        $body = $response->getBody();
        $datapost = $request->getParsedBody();
        //Check the validation
        if($request->getAttribute('has_errors')){
            $errors = $request->getAttribute('errors');
            $data = [
                'status' => 'error',
                'message' => 'Parameter is not valid! ',
                'problem' => json_encode($errors)
            ];
        } else {
            $user = new User();
            $user->username = $datapost['username'];
            $user->password = $datapost['password'];
            $user->password2 = $datapost['password2'];
            $user->email = $datapost['email'];
            $data = $user->register();
        }

        return $this->view->render($response, "register.twig", $data);
    })->add(new Validation(validator::register()))->add($container->get('csrf'));

    // Dashboard page
    $app->get('/dashboard', function (Request $request, Response $response) {
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, EtagHelper::updateByMinute());
        return $this->view->render($response, "dashboard.twig", []);
    })->setName("/dashboard")->add(new SessionCheck($app->getContainer()->get('router')));

    // Logout
    $app->get('/logout', function (Request $request, Response $response) {
        $body = $response->getBody();
        $sh = new SessionHelper();
        $sh->destroy();
        $url = $request->getUri()->withPath($this->router->pathFor('/login'));
        return $response->withRedirect($url);
    })->setName("/logout");

    // Verify
    $app->post('/user/verify', function (Request $request, Response $response) {
        $body = $response->getBody();
        $datapost = $request->getParsedBody();
        $user = new User();
        $user->username = $datapost['username'];
        $body->write(json_encode($user->verify()));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/user/verify");

    // Verify email
    $app->post('/user/verify/email', function (Request $request, Response $response) {
        $body = $response->getBody();
        $datapost = $request->getParsedBody();
        $user = new User();
        $user->email = $datapost['email'];
        $body->write(json_encode($user->verifyEmail()));
        return $response->withStatus(200)
        ->withHeader('Content-Type','application/json; charset=utf-8')
        ->withBody($body);
    })->setName("/user/verify/email");

    // Get Forgot page
    $app->get('/user/forgot', function (Request $request, Response $response) {
        $body = $response->getBody();
        $data = [
            'status' => '',
            'message' => ''
        ];
        return $this->view->render($response, "forgot.twig", $data);
    })->setName("/user/forgot")->add($container->get('csrf'));

    // Process forgot key
    $app->post('/user/forgot', function (Request $request, Response $response) {
        $body = $response->getBody();
        $datapost = $request->getParsedBody();
        $email = $datapost['email'];
        $user = new User();
        $user->email = $email;
        $data = $user->generateForgotKey();
        if($data['status'] == 'success'){
            $linkverify = $request->getUri()->withPath($this->router->pathFor('/user/forgot/verify',['key'=>$data['key']]));
            $mailer = new Mailer($this->settings);
            $mailer->addAddress = $email;
            $mailer->subject = 'Request reset password';
            $mailer->body = '<html><body><p>You have already requested to reset password.<br /><br />
            Here is the link to reset: <a href="'.$linkverify.'" target="_blank"><b>'.$linkverify.'</b></a>.<br /><br />
            
            Just ignore this email if You don\'t want to reset password. Link will be expired in 3 days.<br /><br /><br />
            Thank You<br />
            '.$this->settings['app']['name'].'</p></body></html>';
            $result = $mailer->send();
        } else {
            $result = $data;
        }
        return $this->view->render($response, "forgot.twig", $result);
    })->add($container->get('csrf'));

    $app->get('/user/forgot/verify/{key}', function (Request $request, Response $response) {
        return $this->view->render($response, "forgot-verify.twig", $result);
    })->setName("/user/forgot/verify");