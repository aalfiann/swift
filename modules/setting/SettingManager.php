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

    protected $basemain;

    public function __construct(){
        $this->basemain = dirname(dirname(dirname(__FILE__)));
    }

    /**
     * Check the directory is valid or not
     * 
     * @return bool
     */
    public function isDir($path){
        return is_dir($this->basemain.DIRECTORY_SEPARATOR.$path);
    }

    /**
     * Save App setting
     * 
     * @return array
     */
    public function saveApp(){
        if(!$this->isDir($this->template_handler)){
            return [
                'status' => 'error',
                'message' => 'Template Handler path is not found!'
            ];
        }
        if(!$this->isDir($this->template_folder)){
            return [
                'status' => 'error',
                'message' => 'Template Folder path is not found!'
            ];
        }
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
     * Save template variable
     * 
     * @return array
     */
    public function saveTemplate(){
        $result = AppConfig::set('template.json',$this->data);
        if($result){
            return [
                'status' => 'success',
                'message' => 'Saving Template variable is successful!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Saving Template variable is failed!'
        ];
    }

    /**
     * Clear template variable
     * 
     * @return array
     */
    public function clearTemplate(){
        $result = AppConfig::clear('template.json');
        if($result){
            return [
                'status' => 'success',
                'message' => 'Template variable is successful cleared!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Template variable is failed to clear!'
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