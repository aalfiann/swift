<?php
namespace modules\user;
use \aalfiann\Filebase;
/**
 * User Manager class
 *
 * @package    swift-user
 * @author     M ABD AZIZ ALFIAN <github.com/aalfiann>
 * @copyright  Copyright (c) 2019 M ABD AZIZ ALFIAN
 * @license    https://github.com/aalfiann/swift-modules-user/blob/master/LICENSE.md  MIT License
 */

class UserManager extends UserHelper {

    /**
     * Add User
     * 
     * @return array
     */
    public function add() {
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
            'dir' => $this->getDataSource()
        ]);

        if (!$user->has($username)) {
            if(!$this->isEmailRegistered()) {
                $item = $user->get($username);
                $item->created_at = strtotime(date('Y-m-d H:i:s'));
                $item->username = $username;
                $item->email = $email;
                $item->hash = $this->hashPassword($username,$password);
                $item->status = 'active';
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
     * Update User
     * 
     * @return array
     */
    public function update() {
        $username = $this->username;
        $email = $this->email;
        $status = $this->status;
        $updated_by = $this->updated_by;

        $user = new \Filebase\Database([
            'dir' => $this->getDataSource()
        ]);

        if ($user->has($username)) {
            if(!$this->isEmailRegistered($username)) {
                $item = $user->get($username);
                $item->username = $username;
                $item->email = $email;
                $item->status = $status;
                $item->updated_at = strtotime(date('Y-m-d H:i:s'));
                $item->updated_by = $updated_by;
                if($item->save()){
                    $data = [
                        'status' => 'success',
                        'message' => 'Update User successful!'
                    ];
                } else {
                    $data = [
                        'status' => 'error',
                        'message' => 'Process failed to update, Please try again later!'
                    ];
                }
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Email is already used by other user!'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Can\'t update, User is not found!'
            ];
        }
        return $data;
    }

    /**
     * Delete User
     * 
     * @return array
     */
    public function delete() {
        $username = $this->username;

        $user = new \Filebase\Database([
            'dir' => $this->getDataSource()
        ]);

        if ($user->has($username)) {
            $item = $user->get($username);
            if($item->delete()){
                $data = [
                    'status' => 'success',
                    'message' => 'Delete User successful!'
                ];
            } else {
                $data = [
                    'status' => 'error',
                    'message' => 'Process failed to delete, Please try again later!'
                ];
            }
        } else {
            $data = [
                'status' => 'error',
                'message' => 'Can\'t delete, User is not found!'
            ];
        }
        return $data;
    }

    /**
     * Index Data User
     * 
     * @return array
     */
    public function index() {
        $search = $this->search;
        $page = $this->page;
        $itemperpage = $this->itemperpage;
        $offset = (((($page-1)*$itemperpage) <= 0)?0:(($page-1)*$itemperpage));

        $user = new \Filebase\Database([
            'dir' => $this->getDataSource()
        ]);

        // List before pagination
        $list1 = $user->query()->select('username,email,status,created_at,updated_at')
            ->where('username','LIKE',$search)
            ->orWhere('email','LIKE',$search)
            ->orWhere('status','LIKE',$search);

        // List after pagination
        $list2 = $list1->limit($itemperpage,$offset)
            ->orderBy('created_at','DESC');

        // total records
        $total_records = $list1->count();
        // total page
        $total_pages = ceil($total_records/$itemperpage);

        if(!empty($list2->results())){
            return [
                'result' => $list2->results(),
                'status' => 'success',
                'message' => 'Data found!',
                'metadata' => [
                    'record_total' => $total_records,
                    'record_count' => $list2->count(),
                    'number_item_first' => (int)((($page-1)*$itemperpage)+1),
                    'number_item_last' => (int)((($page-1)*$itemperpage)+$list2->count()),
                    'itemperpage' => (int)$itemperpage,
                    'page_now' => (int)$page,
                    'page_total' => $total_pages
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Data not found!'
            ];
        }
    }

    /**
     * Read Single Data User
     * 
     * @return array
     */
    public function read() {
        $username = $this->username;

        $user = new \Filebase\Database([
            'dir' => $this->getDataSource()
        ]);

        if ($user->has($username)) {
            $item = $user->get($username);
            $data = $item->toArray();
            unset($data['hash']); // remove hashed password
            $data['created_at'] = $item->createdAt();
            $data['updated_at'] = $item->updatedAt(); 
            return [
                'result' => $data,
                'status' => 'success',
                'message' => 'Data found!'
            ];
        }
        return [
            'status' => 'error',
            'message' => 'Data not found!'
        ];
    }

    /**
     * Show data option for Manage User
     * 
     * @return array
     */
    public function optionStatus(){
        return [
            'active',
            'suspended'
        ];
    }

}