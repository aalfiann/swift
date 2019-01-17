<?php
namespace modules\user;
use \aalfiann\Filebase;

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
            $item = $user->get($username);
            $item->username = $username;
            $item->email = $email;
            $item->hash = $this->hashPassword($username,$password);
            if($item->save()){
                $data = [
                    'status' => 'success',
                    'message' => 'Register user successful!'
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Register user failed!'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Register user failed! Username is already taken!'
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
}