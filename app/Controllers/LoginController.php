<?php

/**
 * login controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

class LoginController 
{
    public function view() {
        require_once viewsPath() . 'Login/Login.php';
    }

    public function logout() {
        $strStorage = '';
        $strStorage = $strStorage . '<script>';
        $strStorage = $strStorage . 'localStorage.clear();';
        $strStorage = $strStorage . '</script>';
        
        echo $strStorage;

        session_start();
        session_destroy();
        
        require_once viewsPath() . 'Login/Login.php';
    }
}