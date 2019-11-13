<?php

/**
 * states controller
 *
 * @author Giovane Pessoa
 */

namespace App\Controllers;

use App\Models\StatesModel;

class StatesController
{
    public function view() {
        \App\View::make('States/View');
    }

    public function create() {
        
    }

    public function read($id) {
        // crud task for selection in the database
        $states = StatesModel::read($id);

        if (count($states) > 0) {
            foreach ($states as $item) {
                $id = $item['id'];
                $name = $item['name'];
                $initials = $item['initials'];

                $json[] = array(
                    'id' => $id,
                    'name' => "{$name}",
                    'initials' => "{$initials}"
                );
            }
            return $json;
        }
    }

    public function update() {
        
    }

    public function delete() {
        
    }
}