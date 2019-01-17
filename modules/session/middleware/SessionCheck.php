<?php 
namespace modules\session\middleware;
use \modules\session\helper\SessionHelper;
use \Slim\Router;
    /**
     * A class for session check
     *
     * @package    swift-session
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/swift-modules-session/blob/master/license.md  MIT License
     */
    class SessionCheck {

        public function __construct(Router $router){
            $this->router = $router;
        }

        /**
         * SessionCheck middleware invokable class
         * 
         * @param \Psr\Http\Message\ServerRequestInterface  $request    PSR7 request
         * @param \Psr\Http\Message\ResponseInterface       $response   PSR7 response
         * @param callable                                  $next       Next middleware
         * 
         * @return \Psr\Http\Message\ResponseInterface
         */
        public function __invoke($request, $response, $next){
            $sh = new SessionHelper();
            if($sh->exists('username')){
                $response = $next($request, $response);    
                return $response;
            }
            $url = $this->router->pathFor('/login');
            return $response->withRedirect($url);
        }
    }