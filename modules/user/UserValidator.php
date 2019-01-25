<?php
namespace modules\user;
use \Respect\Validation\Validator as v;
/**
 * UserValidator class
 *
 * @package    swift-user
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/swift-modules-user/blob/master/LICENSE.md  MIT License
 */
class UserValidator {
    public static function register(){
        return [
            'username' => v::alnum()->noWhitespace()->length(3, 20),
            'email' => v::email()
        ];
    }

    public static function index(){
        return [
            'page' => v::intVal(),
            'itemperpage' => v::intVal(),
            'search' => v::length(0, 20)
        ];
    }

    public static function userinfo(){
        return [
            'username' => v::alnum()->noWhitespace()->length(3, 20)
        ];
    }
}