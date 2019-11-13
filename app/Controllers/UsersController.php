<?php

/**
 * users controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\UsersModel;
use App\Authorize;

class UsersController
{
    public function view() {
        \App\View::make('Users/View');
    }

    public function create() {
        
    }

    public function read() {
        
    }

    public function readUsersAuthentication($email, $password) {
        // crud task for selection in the database
        $users = UsersModel::readUsersAuthentication($email);

        if (!empty($users)) {
            $secretKey = $users[0]['secretkey'];
            $hash = $users[0]['hash'];
            if (password_verify($password, $hash)) {
                $dataUser = array();

                array_push($dataUser, $users[0]['user_url_image']);
                array_push($dataUser, $users[0]['user_id']);
                array_push($dataUser, $users[0]['user_name']);
                array_push($dataUser, $users[0]['user_email']);
                array_push($dataUser, $users[0]['user_company_id']);
                array_push($dataUser, $users[0]['user_company_name']);
                array_push($dataUser, $users[0]['secretkey']);
                array_push($dataUser, Authorize::create($secretKey));
                array_push($dataUser, $users[0]['sectors_id']);
                array_push($dataUser, $users[0]['sectors_name']);
                array_push($dataUser, $users[0]['status_id']);

                return $dataUser;
            } else {
                return null;
            }
        }
        else{
            return null;
        }       
    }
    
    public function readUsersPermissions($id, $column) {
        // crud task for selection in the database
        return UsersModel::readUsersPermissions($id, $column)[0];
    }
    
    public function readColumnsUsersPermissions() {
        // crud task for selection in the database
        return UsersModel::readColumnsUsersPermissions();
    }

    public function update() {
        
    }

    public function delete() {
        
    }
}