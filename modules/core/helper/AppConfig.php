<?php 
namespace modules\core\helper;
use \aalfiann\Filebase;

    class AppConfig {

        public static function dirPath(){
            return dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'config';
        }

        /**
         * Delete nested array with keys
         */
        private static function delete_array(&$array, $keys){
            $key = array_shift($keys);
            if (count($keys) == 0)
                unset($array[$key]);
            else
                self::delete_array($array[$key], $keys);
        }

        /**
         * Check directory config
         */
        public static function checkConfigDir(){
            if(!is_dir(self::dirPath())) mkdir(self::dirPath(),0775);
        }

        /**
         * Get filePath config
         * 
         * @param filename is the filename of config. Ex. config.json
         * 
         * @return string
         */
        public static function filePath($filename){
            self::checkConfigDir();
            $file = self::dirPath().DIRECTORY_SEPARATOR.$filename;
            return $file;
        }

        /**
         * Check file config is exists
         * 
         * @param filename is the filename of config. Ex. config.json
         * 
         * @return bool
         */
        public static function has($filename){
            return is_file(self::filePath($filename));
        }

        /**
         * Load config
         * 
         * @param filename is the filename of config. Ex. config.json
         * 
         * @return array
         */
        public static function load($filename){
            return json_decode(\Filebase\Filesystem::read(self::filePath($filename)),true);
        }

        /**
         * Set config variable 
         * 
         * @param filename is the filename of config. Ex. config.json
         * @param data is the new data array to set the config variable
         * 
         * @return bool
         */
        public static function set($filename,$data){
            $content = json_decode(\Filebase\Filesystem::read(self::filePath($filename)),true);
            if($content){
                if(is_array($data)){
                    foreach($data as $key => $value){
                        $content[$key] = $value;
                    }
                    return \Filebase\Filesystem::write(self::filePath($filename),json_encode($content,JSON_UNESCAPED_SLASHES));
                } else {
                    return false;
                }
            }
            return \Filebase\Filesystem::write(self::filePath($filename),json_encode($data,JSON_UNESCAPED_SLASHES));
        }

        /**
         * Append config variable (similar with set function but not rewrite the exists element)
         * 
         * @param filename is the filename of config. Ex. config.json
         * @param data is the new data array to append the config variable
         * 
         * @return bool
         */
        public static function append($filename,$data){
            $content = json_decode(\Filebase\Filesystem::read(self::filePath($filename)),true);
            if($content){
                if(is_array($data)){
                    foreach($data as $key => $value){
                        if (!array_key_exists($key, $content)){
                            $content[$key] = $value;
                        }
                    }
                    return \Filebase\Filesystem::write(self::filePath($filename),json_encode($content,JSON_UNESCAPED_SLASHES));
                } else {
                    return false;
                }
            }
            return \Filebase\Filesystem::write(self::filePath($filename),json_encode($data,JSON_UNESCAPED_SLASHES));
        }

        /**
         * Delete config variable
         * 
         * @param filename is the filename of config. Ex. config.json
         * @param key is the config key, could be string (separated with dot) or array. Ex. in string "global.app.name"
         * 
         * @return bool
         */
        public static function delete($filename,$key){
            $content = json_decode(\Filebase\Filesystem::read(self::filePath($filename)),true);
            if($content){
                if(is_array($key)){
                    delete($content,$key);
                } else {
                    $keys = explode('.', $key);
                    delete($content,$keys);
                }
                return \Filebase\Filesystem::write(self::filePath($filename),json_encode($content,JSON_UNESCAPED_SLASHES));
            }
            return false;
        }

        /**
         * Clear config variable
         * 
         * @param filename is the filename of config. Ex. config.json
         * 
         * @return bool
         */
        public static function clear($filename){
            return \Filebase\Filesystem::write(self::filePath($filename),json_encode('',JSON_UNESCAPED_SLASHES));
        }
    }