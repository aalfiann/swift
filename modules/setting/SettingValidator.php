<?php
namespace modules\setting;
use \Respect\Validation\Validator as v;
/**
 * SettingValidator class
 *
 * @package    swift-setting
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/swift-modules-setting/blob/master/LICENSE.md  MIT License
 */
class SettingValidator {
    public static function app(){
        return [
            'name' => v::stringType()->length(3,20),
            'language' => v::stringType()->alpha()->lowercase()->length(2),
            'timezone' => v::stringType()->length(3, 20),
            'http_max_age' => v::intVal()->length(1, 10),
            'template_handler' => v::stringType()->length(3,250),
            'template_folder' => v::stringType()->length(3,250)
        ];
    }

    public static function smtp(){
        return [
            'host' => v::stringType()->domain(),
            'autotls' => v::boolVal(),
            'auth' => v::boolVal(),
            'secure' => v::stringType()->length(1, 10),
            'port' => v::intVal()->length(1,6),
            'defaultnamefrom' => v::stringType()->length(3,50),
            'username' => v::stringType()->length(0,50),
            'password' => v::stringType()->length(0,250),
            'debug' => v::intVal()->length(1,1)
        ];
    }

    public static function template(){
        return [
            'json' => v::json()
        ];
    }

}
