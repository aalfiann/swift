<?php
namespace modules\setting;
use \modules\core\helper\AppConfig;

/**
 * Setting Manager class
 *
 * @package    swift-setting
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/swift-modules-setting/blob/master/LICENSE.md  MIT License
 */
class SettingManager {
    /**
     * Save App setting
     * 
     * @return array
     */
    public function saveApp(){
        $result = AppConfig::set('app.json',[
            'name' => $this->name,
            'language' => $this->language,
            'timezone' => $this->timezone,
            'log.level' => $this->log_level,
            'http.max-age' => $this->http_max_age,
            'template.handler' => $this->template_handler,
            'template.folder' => $this->template_folder
        ]);
        if($result){
            return [
                'status' => 'success',
                'message' => 'Saving App setting is successful!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Saving App setting is failed!'
        ];
    }

    /**
     * Save SMTP setting
     * 
     * @return array
     */
    public function saveSmtp(){
        $result = AppConfig::set('smtp.json',[
            'host' => $this->host,
            'autotls' => $this->autotls,
            'auth' => $this->auth,
            'secure' => $this->secure,
            'port' => $this->port,
            'defaultnamefrom' => $this->defaultnamefrom,
            'username' => $this->username,
            'password' => $this->password,
            'debug' => $this->debug
        ]);
        if($result){
            return [
                'status' => 'success',
                'message' => 'Saving SMTP setting is successful!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Saving SMTP setting is failed!'
        ];
    }

    /**
     * Save template setting
     * 
     * @return array
     */
    public function saveTemplate(){
        $result = AppConfig::set('template.json',$this->data);
        if($result){
            return [
                'status' => 'success',
                'message' => 'Saving Template setting is successful!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Saving Template setting is failed!'
        ];
    }

    /**
     * Load setting
     * 
     * @param filename is the filename of config. Ex. app.json
     * 
     * @return array
     */
    public function load($filename){
        if(\modules\core\helper\AppConfig::has($filename)){
            $data = AppConfig::load($filename);
            if(!empty($data)){
                return [
                    'result' => $data,
                    'status' => 'success',
                    'message' => 'Data is found!'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Can\'t read the data setting!'
                ];
            }
        }
        return [
            'status' => 'error',
            'message' => 'Data is not found!'
        ];
    }
}