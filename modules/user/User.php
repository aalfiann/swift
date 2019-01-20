<?php
namespace modules\user;
use \modules\genuid\Genuid;
use \modules\mailer\Mailer;
use \aalfiann\Filebase;
/**
 * User class
 *
 * @package    swift-user
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/swift-modules-user/blob/master/LICENSE.md  MIT License
 */
class User {

    /** 
     * HashPassword is to secure your login and password
     *
     * @param $username : input username
     * @param $password : input password
     * @return string Hashed Password
     */
    public function hashPassword($username,$password) {
        $options = [
            'cost' => 8
        ];
        return password_hash($username.$password, PASSWORD_BCRYPT, $options);
    }

    /** 
     * Verify Password is to verify your login and password is match or not
     *
     * @param $username : input username
     * @param $password : input password
     * @param $hash : your password hash saved in database
     * @return boolean true / false
     */
    public function verifyPassword($username,$password,$hash) {
        return password_verify($username.$password, $hash);
    }

    /**
     * Login user
     * 
     * @return array
     */
    public function login() {
        $username = $this->username;
        $password = $this->password;

        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        if ($user->has($username)) {
            $item = $user->get($username);
            if($this->verifyPassword($username,$password,$item->hash)) {
                return [
                    'status' => 'success',
                    'message' => 'Login successful!'
                ];
            }
        }
        return [
            'status' => 'error',
            'message' => 'Wrong Username or Password!'
        ];
    }

    /**
     * Register user
     * 
     * @return array
     */
    public function register() {
        $username = $this->username;
        $password = $this->password;
        $password2 = $this->password2;
        $email = $this->email;

        if($password != $password2) {
            return [
                'status' => 'error',
                'message' => 'Password is not match!'
            ];
        }

        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        if (!$user->has($username)) {
            if(!$this->isEmailRegistered()) {
                $item = $user->get($username);
                $item->username = $username;
                $item->email = $email;
                $item->hash = $this->hashPassword($username,$password);
                $item->auth = [
                        [
                            'pattern' => '/dashboard',
                            'method' => ['GET','POST']
                        ],
                        [
                            'pattern' => '/my-profile',
                            'method' => ['GET','POST']
                        ]
                    ];
                if($item->save()){
                    $data = [
                        'status' => 'success',
                        'message' => 'Register user successful!'
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Process saving failed, please try again!'
                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Email address already taken!'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Username is already taken!'
            ];
        }
        return $data;
    }

    /**
     * Verify user
     * 
     * @return array
     */
    public function verify() {
        $username = $this->username;

        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        if (!$user->has($username)) {
            $data = [
                'status' => 'success',
                'message' => 'Username is available.'
            ];
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Username is already taken!'
            ];
        }
        return $data;
    }

    /**
     * Determine registered email
     * 
     * @return bool
     */
    public function isEmailRegistered(){
        $email = $this->email;
        
        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        $list = $user->query()->where('email','=',$email)->limit(1)->results();
        if(!empty($list)){
            if($list[0]['email'] == $email) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verify user email
     * 
     * @return array
     */
    public function verifyEmail(){
        if(!$this->isEmailRegistered()) {
            $data = [
                'status' => 'success',
                'message' => 'Email address is available.'
            ];
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Email address is already taken!'
            ];
        }
        return $data;
    }

    /**
     * Generate forgot key
     * 
     * @return array
     */
    public function generateForgotKey(){
        $email = $this->email;
        if($this->isEmailRegistered()) {
            $guid = new Genuid();
            $key = $guid->generate_short_dechex();
            $expired = strtotime(date('Y-m-d H:i:s').' + 3 day');
            $forgot = new \Filebase\Database([
                'dir' => 'storage/user_forgot'
            ]);
    
            if (!$forgot->has($key)) {
                $item = $forgot->get($key);
                $item->email = $email;
                $item->expired = $expired;
                $item->key = $key;
                if($item->save()){
                    $data = [
                        'status' => 'success',
                        'key' => $key,
                        'expired' => $expired,
                        'message' => 'Key will expired in 3 days.'
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Process saving failed, please try again!'
                    ];    
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Process generate failed, please try again!'
                ];  
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Email address not found! Please try again!'
            ];
        }
        return $data;
    }

    /**
     * Determine Forgot Key is active or not
     * 
     * @return bool
     */
    public function isForgotKeyActive(){
        $key = $this->key;
        
        $user = new \Filebase\Database([
            'dir' => 'storage/user_forgot'
        ]);

        if ($user->has($key)) {
            $item = $user->get($key);
            if(strtotime(date('Y-m-d H:i:s')) <= $item->expired){
                return true;
            } else {
                $item->delete();
                return false;
            }
        }
        return false;
    }

    /**
     * Get Username by Forgot Key
     * 
     * @return string
     */
    public function getUsernameByForgotKey(){
        $key = $this->key;

        //get email
        $user_forgot = new \Filebase\Database([
            'dir' => 'storage/user_forgot'
        ]);

        $item = $user_forgot->get($key);
        $email = $item->email;

        //get username
        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        $list = $user->query()->where('email','=',$email)->limit(1)->results();
        if(!empty($list)){
            if(!empty($list[0]['username'])){
                return $list[0]['username'];
            }
        }
        return '';
    }

    /**
     * Reset Password
     * 
     * @return array
     */
    public function resetPassword(){
        $username = $this->username;
        $password = $this->password;
        $password2 = $this->password2;
        
        if($password != $password2) {
            return [
                'status' => 'error',
                'message' => 'Password is not match!'
            ];
        }

        $user = new \Filebase\Database([
            'dir' => 'storage/user'
        ]);

        if ($user->has($username)) {
            $item = $user->get($username);
            $item->hash = $this->hashPassword($username,$password2);
            if($item->save()){
                $this->deleteForgotKey();
                $data = [
                    'status' => 'success',
                    'message' => 'Reset password successful!'
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Process saving failed, please try again!'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Something happened, please reload page!'
            ];
        }
        return $data;
    }

    /**
     * Delete Forgot Key
     * 
     * @return bool
     */
    public function deleteForgotKey(){
        $key = $this->key;
        $user = new \Filebase\Database([
            'dir' => 'storage/user_forgot'
        ]);
        if ($user->has($key)) {
            $item = $user->get($key);
            return $item->delete();
        }
        return false;
    }

    /**
     * Change Password
     * 
     * @return bool
     */
    public function changePassword(){
        $username = $this->username;
        $password = $this->password;
        $password2 = $this->password2;

        if(!empty($username) && (!empty($password) || !empty($password2))){
            $user = new \Filebase\Database([
                'dir' => 'storage/user'
            ]);
    
            if ($user->has($username)) {
                $item = $user->get($username);
                $item->hash = $this->hashPassword($username,$password2);
                if($item->save()){
                    return true;
                }
            }
        }
        return false;
    }
}