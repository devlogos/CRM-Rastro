<?php

/**
 * default view
 *
 * @author Giovane Pessoa
 */

namespace App;

class View
{
    public static function make($viewName, array $customVars = array()) {        
        extract($customVars);
        
        require_once viewsPath() . 'Template.php';
    }
}