<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \modules\session\middleware\SessionCheck;
use \modules\session\helper\SessionHelper;
use \modules\core\helper\EtagHelper;
use \modules\user\User;
use \Respect\Validation\Validator as v;
use \DavidePastore\Slim\Validation\Validation;

    //Create validator
    $usernameValidator = v::alnum()->noWhitespace()->length(3, 20);
    $emailValidator = v::email();
    $registerValidators = array(
        'username' => $usernameValidator,
        'email' => $emailValidator
    );

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
    })->add(new Validation($registerValidators))->add($container->get('csrf'));

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