<?php

namespace modules\genuid;                           //Make sure namespace is same structure with parent directory
use \modules\genuid\UUID as UUID;                   //UUID class

	/**
     * Genuid class
     *
     * @package    swift-genuid
     * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
     * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
     * @license    https://github.com/aalfiann/swift-modules-genuid/blob/master/LICENSE.md  MIT License
     */
    class Genuid {

        var $namespace,$name,$prefix='',$suffix='',$abs=true,$length=13;

        /**
         * Numeric randomizer to make more unique
         * Note: This randomizer is act like salt.
         * 
         * @return string with 10 digits
         */
        public function numeric_randomizer(){
            $data = mt_rand();
            $pad = (10 - strlen($data));
            if($pad > 0){
                $leading = "";
                for ($i=1;$i<=$pad;$i++){
                    $leading .= '0';
                }
                return str_replace('-','00',str_pad($data, 10, $leading, STR_PAD_LEFT));
            }
            return str_replace('-','0',$data);
        }


        /**
         * Generate UUID V3
         * Note: V3 is use MD5
         * 
         * @property namespace is the uuid base
         * @property name is the data source
         * 
         * @return string uuid version 3
         */
        public function generate_uuidV3() {
            return UUID::v3($this->namespace,$this->name);
        }

        /**
         * Generate UUID V4
         * 
         * @return string uuid version 4
         */
        public function generate_uuidV4() {
            return UUID::v4();
        }

        /**
         * Generate UUID V5
         * Note: V5 is use SHA1
         * 
         * @property namespace is the uuid base
         * @property name is the data source
         * 
         * @return string uuid version 5
         */
        public function generate_uuidV5() {
            return UUID::v5($this->namespace,$this->name);
        }

        /**
         * Generate Short ID very fast with uniqid + crc32 + dechex + randomizer
         * 
         * @return string alphanumeric with 8 chars
         */
        public function generate_short_dechex(){
            $data = dechex(crc32(uniqid($this->numeric_randomizer(),true)));
            $pad = (8 - strlen($data));
            if($pad > 0){
                $leading = "";
                for ($i=1;$i<=$pad;$i++){
                    $leading .= '0';
                }
                return $this->prefix.str_replace('-','00',str_pad($data, 8, $leading, STR_PAD_LEFT)).$this->suffix;
            }
            return $this->prefix.str_replace('-','0',$data).$this->suffix;
        }

        /**
         * Generate Short ID very fast with uniqid + crc32 + base_convert + randomizer
         * 
         * @return string alphanumeric with 7 chars
         */
        public function generate_short_base(){
            $data = base_convert(crc32(uniqid($this->numeric_randomizer(),true)), 10, 36);
            $pad = (7 - strlen($data));
            if($pad > 0){
                $leading = "";
                for ($i=1;$i<=$pad;$i++){
                    $leading .= '0';
                }
                return $this->prefix.str_replace('-','00',str_pad($data, 7, $leading, STR_PAD_LEFT)).$this->suffix;
            }
            return $this->prefix.str_replace('-','0',$data).$this->suffix;
        }

        /**
         * Generate Unique ID very fast with uniqid + more_entropy + randomizer
         * 
         * @return string alphanumeric with 32 chars
         */
        public function generate_uniqid_long(){
            return $this->prefix.str_replace('.','',uniqid($this->numeric_randomizer(),true)).$this->suffix;
        }

        /**
         * Generate Unique ID very fast with uniqid + randomizer
         * 
         * @return string alphanumeric with 23 chars
         */
        public function generate_uniqid_simple(){
            return $this->prefix.uniqid($this->numeric_randomizer()).$this->suffix;
        }

        /**
         * Generate Short Numeric ID very fast with uniqid + crc32 + randomizer
         * 
         * @return string numeric with 10 digits
         */
        public function generate_uniqid_numeric(){
            $salt = $this->numeric_randomizer();
            $data = (($this->abs)?abs(crc32(uniqid($salt))):crc32(uniqid($salt)));
            $pad = (10 - strlen($data));
            if($pad > 0){
                $leading = "";
                for ($i=1;$i<=$pad;$i++){
                    $leading .= '0';
                }
                return $this->prefix.str_replace('-','00',str_pad($data, 10, $leading, STR_PAD_LEFT)).$this->suffix;
            }
            return $this->prefix.str_replace('-','0',$data).$this->suffix;
        }

        /**
         * Generate Unique ID very fast with random or pseudo bytes
         * 
         * @property length you can adjust the digits to below 13 length, but carefull about the uniqueness, default will return 13 chars.
         * 
         * @return string alphanumeric with 13 chars
         */
        public function generate_unique_custom() {
            if (function_exists("random_bytes")) {
                $bytes = random_bytes(ceil($this->length / 2));
            } elseif (function_exists("openssl_random_pseudo_bytes")) {
                $bytes = openssl_random_pseudo_bytes(ceil($this->length / 2));
            } else {
                throw new Exception("no cryptographically secure random function available");
            }
            return $this->prefix.substr(bin2hex($bytes), 0, $this->length).$this->suffix;
        }
    }