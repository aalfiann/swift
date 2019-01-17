# Swift

[![Version](https://img.shields.io/badge/dev-1.0.0-red.svg)](https://github.com/aalfiann/swift)
[![Total Downloads](https://poser.pugx.org/aalfiann/swift/downloads)](https://packagist.org/packages/aalfiann/swift)
[![License](https://poser.pugx.org/aalfiann/swift/license)](https://github.com/aalfiann/swift/blob/HEAD/LICENSE.md)

A modern, slim, lightweight, simple, flat file or serverless micro framework.  
Serverless **(No database required)** and secured with **CSRF**.  

## Dependencies
- CSRF Guard >> slim/csrf
- TWIG Template >> slim/twig-view
- HTTP Cache >> slim/http-cache
- Logger >> monolog/monolog
- Filebase >> aalfiann/filebase
- Validation >> davidepastore/slim-validation
- Mailer >> phpmailer/phpmailer

## Installation

Install this package via [Composer](https://getcomposer.org/).
```
composer create-project aalfiann/swift [my-app-name]
```

## Getting Started

### How to create new application
- Go to modules directory
- Create new folder `my-app`
- To create routes, you should follow this pattern >> `*.router.php`
- Put the view template to `templates/default` directory
- Done

### How to activate CSRF
CSRF is already integrated in this skeleton :  
1. Create same two routes, GET and POST  
```
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// load contact page
$app->get('/contact', function (Request $request, Response $response) {
    $body = $response->getBody();
    return $this->view->render($response, "contact.twig", []);
})->setName("/contact")->add($container->get('csrf'));

// send message
$app->post('/contact', function (Request $request, Response $response) {
    $body = $response->getBody();
    return $this->view->render($response, "contact.twig", []);
})->add($container->get('csrf'));
```  
2. Put hidden input value in contact form HTML  
```
<input type="hidden" name="{{csrf.keys.name}}" value="{{csrf.name}}">
<input type="hidden" name="{{csrf.keys.value}}" value="{{csrf.value}}">
```  
3. Done

**Note:**  
- Based from `Slim Framework` and the documentation about `Slim` is available on [slimframework.com](http://slimframework.com).
- This is a forked version from the original [aalfiann/slim-skeleton](https://github.com/aalfiann/slim-skeleton).